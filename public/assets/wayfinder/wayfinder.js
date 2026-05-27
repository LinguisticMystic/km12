/**
 * KM12 floor wayfinder — expects window.WAYFINDER_ASSETS.floors (json + image per floor).
 */
var pathCell = 4;
var pathCols = 0;
var pathRows = 0;
var mapW = 816;
var mapH = 1032;
/** Tiled imagelayer size — room/path objects are in this pixel space, not the tile grid box. */
var refW = 793;
var refH = 1123;
var displayW = 595;
var displayH = 842;
/** Display box size for the active floor (matches loaded image when possible). */
var layoutW = 0;
var layoutH = 0;
var scaleX = displayW / refW;
var scaleY = displayH / refH;
var grid = null;
var finder = new PF.AStarFinder({
    allowDiagonal: false,
    dontCrossCorners: true
});

/** Map pixel coords — updated after Tiled JSON load (nearest walkable to default entrance). */
var startPx = { x: 154, y: 928 };
var startX = 12;
var startY = 77;

function updatePathGridSize() {
    pathCols = Math.ceil(refW / pathCell);
    pathRows = Math.ceil(refH / pathCell);
}

function setStatus(msg, isErr) {
    var el = document.getElementById("grid-status");
    if (!el) return;
    el.textContent = msg;
    el.style.color = isErr ? "#c0392b" : "#555";
    el.style.visibility = msg ? "visible" : "hidden";
}

function setMapImageLoading(loading) {
    var loader = document.getElementById("map-loader");
    if (loader) {
        loader.classList.toggle("is-hidden", !loading);
    }
}

function setMapImageReady() {
    var img = document.getElementById("floor-img");
    var loader = document.getElementById("map-loader");
    if (loader) {
        loader.classList.add("is-hidden");
    }
    if (img) {
        img.classList.add("is-ready");
    }
}

function layerByName(map, name) {
    var layers = map.layers || [];
    for (var i = 0; i < layers.length; i++) {
        if (layers[i].name === name) return layers[i];
    }
    return null;
}

function imagelayerSize(map) {
    var layers = map.layers || [];
    for (var i = 0; i < layers.length; i++) {
        var L = layers[i];
        if (L.type === "imagelayer" && L.imagewidth && L.imageheight) {
            return { w: L.imagewidth, h: L.imageheight };
        }
    }
    return null;
}

function updateCoordScale() {
    scaleX = displayW / refW;
    scaleY = displayH / refH;
}

function toDisplayX(mapX) {
    return mapX * scaleX;
}

function toDisplayY(mapY) {
    return mapY * scaleY;
}

function toMapX(displayX) {
    return displayX / scaleX;
}

function toMapY(displayY) {
    return displayY / scaleY;
}

function setLayoutSize(w, h) {
    if (!w || !h) return;
    layoutW = w;
    layoutH = h;
    displayW = w;
    displayH = h;
}

function applyDisplayLayout() {
    if (!layoutW) return;
    var container = document.getElementById("map-container");
    if (container) {
        container.style.aspectRatio = layoutW + " / " + layoutH;
        container.style.maxWidth = layoutW + "px";
    }
    var svg = document.getElementById("floor-plan");
    if (svg) {
        svg.setAttribute("viewBox", "0 0 " + layoutW + " " + layoutH);
    }
    var img = document.getElementById("floor-img");
    if (img) {
        img.setAttribute("width", layoutW);
        img.setAttribute("height", layoutH);
    }
}

/** Map Tiled object coords (refW×refH) to the loaded floor image pixels. */
function reconcileLayoutWithImage(img) {
    displayW = refW;
    displayH = refH;
    if (img && img.naturalWidth > 0 && img.naturalHeight > 0) {
        displayW = img.naturalWidth;
        displayH = img.naturalHeight;
    }
    setLayoutSize(displayW, displayH);
    updateCoordScale();
}

/** Match overlay to the loaded floor image; objects use Tiled imagelayer pixel space. */
function syncDisplayScale(img) {
    if (tiledMapData) {
        applyMapDimensions(tiledMapData);
    }
    reconcileLayoutWithImage(img);
    applyDisplayLayout();
    if (tiledMapData) expandRoomsOverlay(tiledMapData);
}

function safeRoomId(obj) {
    var n = String(obj.name || "").trim();
    if (n) return n.replace(/\s+/g, "-");
    return "room-" + obj.id;
}

function roomTriggerClass(obj) {
    var t = String(obj.type || "").toLowerCase();
    if (t === "orange") return "room-trigger room-orange";
    if (t === "teal") return "room-trigger room-teal";
    return "room-trigger room-purple";
}

function roomMapTopLeft(obj) {
    if (obj.polygon && obj.polygon.length >= 3) {
        var minX = obj.x;
        var minY = obj.y;
        for (var i = 0; i < obj.polygon.length; i++) {
            var px = obj.x + obj.polygon[i].x;
            var py = obj.y + obj.polygon[i].y;
            if (px < minX) minX = px;
            if (py < minY) minY = py;
        }
        return { x: minX, y: minY };
    }
    return { x: obj.x, y: obj.y };
}

function roomLabelText(obj) {
    return String(obj.name || "").trim();
}

function roomLabelOffset(obj, axis) {
    var props = obj.properties || [];
    var key = axis === "y" ? "labelOffsetY" : "labelOffsetX";
    for (var i = 0; i < props.length; i++) {
        if (props[i].name === key) {
            var n = Number(props[i].value);
            return isNaN(n) ? 0 : n;
        }
    }
    return 0;
}

function roomLabelHidden(obj) {
    var props = obj.properties || [];
    for (var i = 0; i < props.length; i++) {
        if (props[i].name === "hideLabel") {
            var v = props[i].value;
            return v === true || v === "true" || v === 1;
        }
    }
    return false;
}

function roomLabelProperty(obj, name) {
    var props = obj.properties || [];
    for (var i = 0; i < props.length; i++) {
        if (props[i].name === name) {
            var v = props[i].value;
            if (v === undefined || v === null || v === "") return null;
            return v;
        }
    }
    return null;
}

function roomLabelColor(obj) {
    var color = roomLabelProperty(obj, "labelColor");
    return color == null ? null : String(color).trim();
}

function roomLabelFontSize(obj) {
    var n = Number(roomLabelProperty(obj, "labelFontSize"));
    return isNaN(n) || n <= 0 ? null : n;
}

var defaultLabelColor = "#fff";
var defaultLabelFontSize = 12;
var defaultLabelBgColor = "#e67e22";

function roomLabelBgColor(obj) {
    var color = roomLabelProperty(obj, "labelBgColor");
    return color == null ? null : String(color).trim();
}

function appendRoomLabel(g, obj) {
    var label = roomLabelText(obj);
    if (!label || roomLabelHidden(obj)) return;
    var tl = roomMapTopLeft(obj);
    var pad = 4;
    var offsetX = roomLabelOffset(obj, "x");
    var offsetY = roomLabelOffset(obj, "y");
    var fontSize = roomLabelFontSize(obj) || defaultLabelFontSize;
    var labelColor = roomLabelColor(obj) || defaultLabelColor;
    var labelBgColor = roomLabelBgColor(obj) || defaultLabelBgColor;
    var group = document.createElementNS("http://www.w3.org/2000/svg", "g");
    group.setAttribute("class", "room-label-group");
    group.setAttribute("pointer-events", "none");

    var bg = document.createElementNS("http://www.w3.org/2000/svg", "rect");
    bg.setAttribute("class", "room-label-bg");
    bg.setAttribute("style", "fill:" + labelBgColor);

    var text = document.createElementNS("http://www.w3.org/2000/svg", "text");
    text.setAttribute("class", "room-label");
    text.setAttribute("x", toDisplayX(tl.x + pad + offsetX));
    text.setAttribute("y", toDisplayY(tl.y + pad + fontSize + 1 + offsetY));
    text.setAttribute("style", "fill:" + labelColor + ";font-size:" + fontSize + "px");
    text.textContent = label;

    group.appendChild(bg);
    group.appendChild(text);
    g.appendChild(group);

    var bb = text.getBBox();
    bg.setAttribute("x", String(bb.x - 4));
    bg.setAttribute("y", String(bb.y - 2));
    bg.setAttribute("width", String(bb.width + 8));
    bg.setAttribute("height", String(bb.height + 4));
    bg.setAttribute("rx", "3");
}

function expandRoomsOverlay(map) {
    var rooms = layerByName(map, "Rooms");
    var g = document.getElementById("rooms-layer");
    if (!g) return;
    while (g.firstChild) g.removeChild(g.firstChild);
    if (!rooms || !rooms.objects) return;

    rooms.objects.forEach(function (obj) {
        var el = null;
        if (obj.polygon && obj.polygon.length >= 3) {
            el = document.createElementNS("http://www.w3.org/2000/svg", "polygon");
            var pts = [];
            for (var p = 0; p < obj.polygon.length; p++) {
                var mx = obj.x + obj.polygon[p].x;
                var my = obj.y + obj.polygon[p].y;
                pts.push(toDisplayX(mx) + "," + toDisplayY(my));
            }
            el.setAttribute("points", pts.join(" "));
        } else if (obj.width > 0 && obj.height > 0) {
            el = document.createElementNS("http://www.w3.org/2000/svg", "rect");
            el.setAttribute("x", toDisplayX(obj.x));
            el.setAttribute("y", toDisplayY(obj.y));
            el.setAttribute("width", obj.width * scaleX);
            el.setAttribute("height", obj.height * scaleY);
        }
        if (!el) return;
        el.setAttribute("id", safeRoomId(obj));
        el.setAttribute("class", roomTriggerClass(obj));
        g.appendChild(el);
        appendRoomLabel(g, obj);
    });
}

function applyMapDimensions(map) {
    var tw = map.tilewidth || 12;
    var th = map.tileheight || 12;
    mapW = map.width * tw;
    mapH = map.height * th;

    var ref = imagelayerSize(map);
    if (ref) {
        refW = ref.w;
        refH = ref.h;
    }
    updatePathGridSize();

    var startObj = findStartObject(map);
    if (startObj) {
        startPx = startObj;
    } else {
        /* Entrance in imagelayer pixel space (same relative spot as old 595×842 prototype). */
        startPx = { x: (112.5 / 595) * refW, y: (757.5 / 842) * refH };
    }
}

/** Walk mesh: only grid cells that intersect a Paths-layer rectangle (no dilation). */
function buildRawWalkFromPaths(map) {
    var paths = layerByName(map, "Paths");
    var raw = [];
    var row;
    var col;
    for (row = 0; row < pathRows; row++) {
        raw[row] = [];
        for (col = 0; col < pathCols; col++) raw[row][col] = false;
    }
    if (!paths || !paths.objects) return raw;

    paths.objects.forEach(function (obj) {
        if (!obj.visible) return;
        if (obj.width <= 0 || obj.height <= 0) return;
        var rx = obj.x;
        var ry = obj.y;
        var rw = obj.width;
        var rh = obj.height;
        for (row = 0; row < pathRows; row++) {
            for (col = 0; col < pathCols; col++) {
                if (rectIntersectsPathCell(rx, ry, rw, rh, col, row)) raw[row][col] = true;
            }
        }
    });
    return raw;
}

function rectIntersectsPathCell(rx, ry, rw, rh, col, row) {
    var tx = col * pathCell;
    var ty = row * pathCell;
    return rx < tx + pathCell && rx + rw > tx && ry < ty + pathCell && ry + rh > ty;
}

function nearestWalkable(raw, ix, iy) {
    if (raw[iy] && raw[iy][ix]) return { x: ix, y: iy };
    var best = null;
    var bestD = 1000000;
    var y;
    var x;
    for (y = 0; y < pathRows; y++) {
        for (x = 0; x < pathCols; x++) {
            if (!raw[y][x]) continue;
            var dx = x - ix;
            var dy = y - iy;
            var dist = dx * dx + dy * dy;
            if (dist < bestD) {
                bestD = dist;
                best = { x: x, y: y };
            }
        }
    }
    return best;
}

function findStartObject(map) {
    var layers = map.layers || [];
    var li;
    for (li = 0; li < layers.length; li++) {
        var L = layers[li];
        if (L.type !== "objectgroup" || !L.objects) continue;
        for (var oi = 0; oi < L.objects.length; oi++) {
            var o = L.objects[oi];
            var nm = String(o.name || "").toLowerCase();
            if (nm === "start" || nm === "entrance" || nm === "entry") {
                if (o.point || (!o.polygon && !o.width && !o.height)) {
                    return { x: o.x, y: o.y };
                }
                if (o.width > 0 && o.height > 0) {
                    return { x: o.x + o.width / 2, y: o.y + o.height / 2 };
                }
            }
        }
    }
    return null;
}

function updateEntranceMarker(snapToGrid) {
    var marker = document.getElementById("entrance-marker");
    if (!marker) return;
    var px = startPx.x;
    var py = startPx.y;
    if (snapToGrid) {
        px = startX * pathCell + pathCell / 2;
        py = startY * pathCell + pathCell / 2;
    }
    marker.setAttribute("cx", toDisplayX(px));
    marker.setAttribute("cy", toDisplayY(py));
}

function buildGridFromTiledMap(map, skipLayout) {
    if (!skipLayout) {
        applyMapDimensions(map);
        reconcileLayoutWithImage(document.getElementById("floor-img"));
        applyDisplayLayout();
        expandRoomsOverlay(map);
    }

    var startObj = findStartObject(map);
    if (startObj) startPx = startObj;

    var raw = buildRawWalkFromPaths(map);

    var ix = Math.floor(startPx.x / pathCell);
    var iy = Math.floor(startPx.y / pathCell);
    ix = Math.max(0, Math.min(pathCols - 1, ix));
    iy = Math.max(0, Math.min(pathRows - 1, iy));

    var startCell = nearestWalkable(raw, ix, iy);
    if (!startCell) {
        setStatus("Paths layer produced no walkable tiles (or map is empty).", true);
        updateEntranceMarker(false);
        return null;
    }
    startX = startCell.x;
    startY = startCell.y;

    var seen = [];
    for (var j = 0; j < pathRows; j++) seen[j] = [];
    var q = [[startCell.x, startCell.y]];
    seen[startCell.y][startCell.x] = true;
    var head = 0;
    while (head < q.length) {
        var c = q[head++];
        var x0 = c[0];
        var y0 = c[1];
        var dirs = [
            [1, 0],
            [-1, 0],
            [0, 1],
            [0, -1]
        ];
        var di;
        for (di = 0; di < 4; di++) {
            var nx = x0 + dirs[di][0];
            var ny = y0 + dirs[di][1];
            if (nx < 0 || nx >= pathCols || ny < 0 || ny >= pathRows) continue;
            if (seen[ny][nx] || !raw[ny][nx]) continue;
            seen[ny][nx] = true;
            q.push([nx, ny]);
        }
    }

    var g = new PF.Grid(pathCols, pathRows);
    for (j = 0; j < pathRows; j++) {
        for (var i = 0; i < pathCols; i++) {
            g.setWalkableAt(i, j, !!seen[j][i]);
        }
    }

    updateEntranceMarker(true);

    return g;
}

function nearestOnGrid(g, tx, ty) {
    if (g.nodes[ty] && g.nodes[ty][tx].walkable) return { x: tx, y: ty };
    var best = null;
    var bestD = 1000000;
    var y;
    var x;
    for (y = 0; y < pathRows; y++) {
        for (x = 0; x < pathCols; x++) {
            if (!g.nodes[y][x].walkable) continue;
            var dx = x - tx;
            var dy = y - ty;
            var dist = dx * dx + dy * dy;
            if (dist < bestD) {
                bestD = dist;
                best = { x: x, y: y };
            }
        }
    }
    return best;
}

/** Prefer path cells inside the clicked room (door stubs); fall back to global nearest. */
function nearestOnGridForRoom(g, aimMapX, aimMapY, mapBounds) {
    var mapCx = aimMapX;
    var mapCy = aimMapY;
    var rx = mapBounds.x;
    var ry = mapBounds.y;
    var rw = mapBounds.width;
    var rh = mapBounds.height;
    var bestIn = null;
    var bestInD = 1000000;
    var y;
    var x;
    for (y = 0; y < pathRows; y++) {
        for (x = 0; x < pathCols; x++) {
            if (!g.nodes[y][x].walkable) continue;
            var px = x * pathCell + pathCell / 2;
            var py = y * pathCell + pathCell / 2;
            if (px < rx || px > rx + rw || py < ry || py > ry + rh) continue;
            var dx = px - mapCx;
            var dy = py - mapCy;
            var dist = dx * dx + dy * dy;
            if (dist < bestInD) {
                bestInD = dist;
                bestIn = { x: x, y: y };
            }
        }
    }
    if (bestIn) return bestIn;
    var tx = Math.floor(aimMapX / pathCell);
    var ty = Math.floor(aimMapY / pathCell);
    return nearestOnGrid(g, tx, ty);
}

var tiledMapData = null;
var currentFloor = null;
var floorImageCache = {};
var floorImagesLoaded = {};
var floorJsonCache = {};

function getFloors() {
    var assets = window.WAYFINDER_ASSETS || {};
    if (assets.floors && assets.floors.length) return assets.floors;
    if (assets.json && assets.png) {
        return [{ id: "default", label: "Floor", json: assets.json, image: assets.png }];
    }
    return [];
}

function clearMapState() {
    grid = null;
    clicksWired = false;
    var pathLine = document.getElementById("path-line");
    if (pathLine) pathLine.setAttribute("points", "");
    var g = document.getElementById("rooms-layer");
    if (g) while (g.firstChild) g.removeChild(g.firstChild);
    buildRoomList(null);
}

function setActiveFloorTab(floorId) {
    document.querySelectorAll(".floor-tab").forEach(function (btn) {
        var active = btn.getAttribute("data-floor-id") === floorId;
        btn.classList.toggle("is-active", active);
        btn.setAttribute("aria-selected", active ? "true" : "false");
    });
}

function buildFloorTabs() {
    var container = document.getElementById("floor-tabs");
    if (!container) return;
    while (container.firstChild) container.removeChild(container.firstChild);

    getFloors().forEach(function (floor) {
        var li = document.createElement("li");
        var btn = document.createElement("button");
        btn.type = "button";
        btn.className = "floor-tab";
        btn.setAttribute("role", "tab");
        btn.setAttribute("data-floor-id", floor.id);
        btn.setAttribute("aria-selected", "false");
        btn.textContent = floor.label;
        btn.addEventListener("click", function () {
            switchFloor(floor);
        });
        li.appendChild(btn);
        container.appendChild(li);
    });
}

function fetchFloorJson(url) {
    return fetch(url, { cache: "no-store" })
        .then(function (res) {
            if (!res.ok) return null;
            return res.json();
        })
        .catch(function () {
            return null;
        });
}

function isImageUrlMatch(img, imageUrl) {
    if (!img || !imageUrl) return false;
    try {
        return new URL(imageUrl, window.location.href).href === new URL(img.src, window.location.href).href;
    } catch (e) {
        return img.src === imageUrl;
    }
}

function isFloorImageReady(imageUrl) {
    return !!floorImagesLoaded[imageUrl];
}

function ensureFloorImage(imageUrl) {
    if (floorImageCache[imageUrl]) {
        return floorImageCache[imageUrl];
    }
    floorImageCache[imageUrl] = new Promise(function (resolve, reject) {
        var preload = new Image();
        function done() {
            floorImagesLoaded[imageUrl] = true;
            resolve(preload);
        }
        function fail() {
            delete floorImageCache[imageUrl];
            reject(new Error("image load failed"));
        }
        preload.onload = done;
        preload.onerror = fail;
        preload.src = imageUrl;
        if (preload.complete && preload.naturalWidth) {
            done();
        }
    });
    return floorImageCache[imageUrl];
}

function getFloorJson(url) {
    if (Object.prototype.hasOwnProperty.call(floorJsonCache, url)) {
        return floorJsonCache[url];
    }
    floorJsonCache[url] = fetchFloorJson(url);
    return floorJsonCache[url];
}

function prefetchAllFloors() {
    getFloors().forEach(function (floor) {
        ensureFloorImage(floor.image);
        getFloorJson(floor.json);
    });
}

function applyFloorImageToDom(imageUrl) {
    var img = document.getElementById("floor-img");
    if (!img) return;
    if (!isImageUrlMatch(img, imageUrl)) {
        img.src = imageUrl;
    }
    setMapImageReady();
}

function switchFloor(floor) {
    if (!floor) return;
    if (currentFloor && currentFloor.id === floor.id) return;

    var floorId = floor.id;
    var imageUrl = floor.image;
    var jsonUrl = floor.json;

    currentFloor = floor;
    setActiveFloorTab(floor.id);
    tiledMapData = null;
    clearMapState();

    if (!isFloorImageReady(imageUrl)) {
        setMapImageLoading(true);
    }

    ensureFloorImage(imageUrl)
        .then(function () {
            if (!currentFloor || currentFloor.id !== floorId) return;
            applyFloorImageToDom(imageUrl);
            onFloorImageReady();
        })
        .catch(function () {
            if (!currentFloor || currentFloor.id !== floorId) return;
            setMapImageLoading(false);
            setStatus("Could not load floor plan image.", true);
        });

    getFloorJson(jsonUrl).then(function (map) {
        if (!currentFloor || currentFloor.id !== floorId) return;
        if (map && validateTiledMap(map, true)) {
            tiledMapData = map;
        } else {
            tiledMapData = null;
        }
        onFloorImageReady();
    });
}

function onFloorImageReady() {
    var img = document.getElementById("floor-img");
    if (!img || !img.complete || !img.naturalWidth) return;
    if (tiledMapData) {
        initNavigation();
    } else {
        syncDisplayScale(img);
        setStatus("", false);
    }
}

function initNavigation() {
    var img = document.getElementById("floor-img");
    if (!img || !img.complete || !img.naturalWidth) return;
    if (!tiledMapData) return;

    syncDisplayScale(img);

    var g;
    try {
        g = buildGridFromTiledMap(tiledMapData, true);
    } catch (e) {
        console.error(e);
        setStatus("Could not build grid from Tiled data: " + (e.message || String(e)), true);
        return;
    }
    if (!g) {
        buildRoomList(tiledMapData);
        return;
    }

    grid = g;
    var walkCount = 0;
    for (var jj = 0; jj < pathRows; jj++) {
        for (var ii = 0; ii < pathCols; ii++) {
            if (g.nodes[jj][ii].walkable) walkCount++;
        }
    }
    console.debug(
        "Walk mesh (Tiled Paths): " + walkCount + " cells, grid " + pathCols + "×" + pathRows + " @ " + pathCell + "px"
    );
    setStatus("", false);

    clicksWired = false;
    buildRoomList(tiledMapData);
    wireClicks();
}

function setActiveRoomListItem(roomId) {
    var items = document.querySelectorAll(".room-list-item");
    for (var i = 0; i < items.length; i++) {
        var btn = items[i];
        if (btn.getAttribute("data-room-id") === roomId) {
            btn.classList.add("is-active");
        } else {
            btn.classList.remove("is-active");
        }
    }
}

function buildRoomList(map) {
    var list = document.getElementById("room-list-items");
    if (!list) return;
    while (list.firstChild) list.removeChild(list.firstChild);
    if (!map) return;

    var rooms = layerByName(map, "Rooms");
    if (!rooms || !rooms.objects) return;

    var named = [];
    rooms.objects.forEach(function (obj) {
        if (roomLabelText(obj)) named.push(obj);
    });
    named.sort(function (a, b) {
        return roomLabelText(a).localeCompare(roomLabelText(b), "lv", { sensitivity: "base" });
    });

    named.forEach(function (obj) {
        var roomId = safeRoomId(obj);
        var li = document.createElement("li");
        var btn = document.createElement("button");
        btn.type = "button";
        btn.className = "room-list-item";
        btn.setAttribute("data-room-id", roomId);
        btn.textContent = roomLabelText(obj);
        li.appendChild(btn);
        list.appendChild(li);
    });
}

function mapBoundsFromRoomTrigger(roomEl) {
    var rect = roomEl.getBBox();
    var mapLeft = toMapX(rect.x);
    var mapTop = toMapY(rect.y);
    return {
        x: mapLeft,
        y: mapTop,
        width: toMapX(rect.x + rect.width) - mapLeft,
        height: toMapY(rect.y + rect.height) - mapTop
    };
}

function navigateToRoomTrigger(roomEl, aimMapX, aimMapY) {
    if (!grid || !roomEl) return;
    var pathLine = document.getElementById("path-line");
    var rect = roomEl.getBBox();
    if (aimMapX == null || aimMapY == null) {
        aimMapX = toMapX(rect.x + rect.width / 2);
        aimMapY = toMapY(rect.y + rect.height / 2);
    }
    var mapBounds = mapBoundsFromRoomTrigger(roomEl);

    var gridBackup = grid.clone();
    var snap = nearestOnGridForRoom(gridBackup, aimMapX, aimMapY, mapBounds);
    if (!snap) {
        pathLine.setAttribute("points", "");
        return;
    }

    gridBackup.setWalkableAt(startX, startY, true);
    var path = finder.findPath(startX, startY, snap.x, snap.y, gridBackup);

    if (path.length > 0) {
        var pointsString = "";
        for (var k = 0; k < path.length; k++) {
            var mapPx = path[k][0] * pathCell + pathCell / 2;
            var mapPy = path[k][1] * pathCell + pathCell / 2;
            pointsString += toDisplayX(mapPx) + "," + toDisplayY(mapPy) + " ";
        }
        pathLine.setAttribute("points", pointsString.trim());
        setActiveRoomListItem(roomEl.id);
    } else {
        pathLine.setAttribute("points", "");
        console.log("No path found to: " + roomEl.id + " at grid " + snap.x + "," + snap.y);
    }
}

var clicksWired = false;
function wireClicks() {
    if (clicksWired || !grid) return;
    clicksWired = true;

    var svg = document.getElementById("floor-plan");
    var triggers = document.querySelectorAll(".room-trigger");

    triggers.forEach(function (room) {
        room.addEventListener("click", function (evt) {
            var rect = this.getBBox();
            var aimDisplayX = rect.x + rect.width / 2;
            var aimDisplayY = rect.y + rect.height / 2;
            if (svg && evt.clientX != null) {
                var pt = svg.createSVGPoint();
                pt.x = evt.clientX;
                pt.y = evt.clientY;
                var ctm = svg.getScreenCTM();
                if (ctm) {
                    var svgPt = pt.matrixTransform(ctm.inverse());
                    aimDisplayX = svgPt.x;
                    aimDisplayY = svgPt.y;
                }
            }
            navigateToRoomTrigger(this, toMapX(aimDisplayX), toMapY(aimDisplayY));
        });
    });

    document.querySelectorAll(".room-list-item").forEach(function (btn) {
        btn.addEventListener("click", function () {
            var roomEl = document.getElementById(btn.getAttribute("data-room-id"));
            navigateToRoomTrigger(roomEl, null, null);
        });
    });
}

function validateTiledMap(map, silent) {
    if (!map || !map.width || !map.height || !map.layers) {
        if (!silent) setStatus("Not a valid Tiled map export.", true);
        return false;
    }
    return true;
}

function boot() {
    buildFloorTabs();
    var floors = getFloors();
    if (floors.length === 0) {
        setStatus("No floors configured.", true);
        return;
    }
    prefetchAllFloors();
    switchFloor(floors[0]);
}

boot();
