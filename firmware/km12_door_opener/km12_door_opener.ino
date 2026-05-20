#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>
#include "secrets.h"

const char* ssid = WIFI_SSID;
const char* password = WIFI_PASSWORD;
const char* serverUrl = SERVER_URL;
const char* apiToken = API_TOKEN;

// Pins
const int statusLEDPin = 15;
const int doorRelayPin = 16;
const int gateRelayPin = 17;
const int buttonPin = 39;

WiFiClientSecure secured_client;

unsigned long lastTimeBotRan = 0;
const unsigned long botInterval = 2000;

bool isDoorOpen = false;
unsigned long doorOpenStartTime = 0;
const unsigned long doorOpenDuration = 10000;

bool isGateOpen = false;
bool gateNextOpen = true;

bool lastButtonState = LOW;
unsigned long buttonPressStart = 0;
bool buttonPressed = false;

bool waitingForTelegram = true;
bool firstMessageHandled = false;
bool hasSaidLabrit = false;

unsigned long ignoreButtonUntil = 0;  // ignore doorbell after gate trigger

unsigned long lastUpdateReceivedTime = 0;
const unsigned long updateTimeout = 5UL * 60 * 1000; // 5 minutes

String apiJsonString(const String& json, const char* key) {
  String search = String("\"") + key + "\":\"";
  int start = json.indexOf(search);
  if (start < 0) {
    return "";
  }
  start += search.length();
  int end = json.indexOf('"', start);
  if (end < 0) {
    return "";
  }
  return json.substring(start, end);
}

void blinkPattern(int times, int delayMs = 200) {
  for (int i = 0; i < times; i++) {
    digitalWrite(statusLEDPin, HIGH);
    delay(delayMs);
    digitalWrite(statusLEDPin, LOW);
    delay(delayMs);
  }
}

void blinkWhile(bool (*condition)(), int interval = 500) {
  while (condition()) {
    digitalWrite(statusLEDPin, HIGH);
    delay(interval);
    digitalWrite(statusLEDPin, LOW);
    delay(interval);
  }
}

bool isWiFiConnecting() {
  return WiFi.status() != WL_CONNECTED;
}

void setup() {
  pinMode(statusLEDPin, OUTPUT);
  pinMode(doorRelayPin, OUTPUT);
  pinMode(gateRelayPin, OUTPUT);
  pinMode(buttonPin, INPUT_PULLUP);
  digitalWrite(statusLEDPin, LOW);

  digitalWrite(doorRelayPin, HIGH);
  digitalWrite(gateRelayPin, HIGH);

  Serial.begin(115200);
  blinkPattern(2); // Startup

  WiFi.begin(ssid, password);
  blinkWhile(isWiFiConnecting, 500);
  blinkPattern(5); // Wi-Fi connected

  secured_client.setInsecure();

  lastUpdateReceivedTime = millis();
}

void handleUpdates() {
  if (WiFi.status() != WL_CONNECTED) {
    return;
  }

  String url = String(serverUrl) + "/api/door-opener/poll";
  HTTPClient http;
  http.begin(secured_client, url);
  http.addHeader("Authorization", String("Bearer ") + apiToken);

  int httpCode = http.GET();

  if (httpCode == 200) {
    lastUpdateReceivedTime = millis();

    if (!hasSaidLabrit) {
      hasSaidLabrit = true;
      blinkPattern(3, 150); // API connected
    }

    String payload = http.getString();
    http.end();

    String command = apiJsonString(payload, "command");

    if (command != "" && !firstMessageHandled) {
      firstMessageHandled = true;
      waitingForTelegram = false;
    }

    if (command == "door") {
      digitalWrite(doorRelayPin, LOW);
      digitalWrite(statusLEDPin, HIGH);
      isDoorOpen = true;
      doorOpenStartTime = millis();
    }
    else if (command == "gate") {
      digitalWrite(gateRelayPin, LOW);
      delay(1000);
      digitalWrite(gateRelayPin, HIGH);

      ignoreButtonUntil = millis() + 30000;  // ignore button after gate trigger
    }
  } else {
    http.end();

    if (millis() - lastUpdateReceivedTime > updateTimeout) {
      lastUpdateReceivedTime = millis();
      Serial.println("Poll timeout — still waiting for API");
    }
  }
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    if (millis() - lastTimeBotRan > botInterval) {
      handleUpdates();
      lastTimeBotRan = millis();
    }

    if (isDoorOpen && millis() - doorOpenStartTime > doorOpenDuration) {
      digitalWrite(doorRelayPin, HIGH);
      digitalWrite(statusLEDPin, LOW);
      isDoorOpen = false;
    }

    // Only check button if we're outside the ignore window
    if (millis() > ignoreButtonUntil) {
      bool currentButtonState = digitalRead(buttonPin);
      if (lastButtonState == LOW && currentButtonState == HIGH) {
        buttonPressStart = millis();
        buttonPressed = true;
      }
      if (lastButtonState == HIGH && currentButtonState == LOW && buttonPressed) {
        if (millis() - buttonPressStart >= 350) {
          String url = String(serverUrl) + "/api/door-opener/doorbell";
          HTTPClient http;
          http.begin(secured_client, url);
          http.addHeader("Authorization", String("Bearer ") + apiToken);
          http.addHeader("Content-Type", "application/json");
          http.POST("{}");
          http.end();
        }
        buttonPressed = false;
      }
      lastButtonState = currentButtonState;
    }
  }

  if (waitingForTelegram && !firstMessageHandled) {
    digitalWrite(statusLEDPin, HIGH);
    delay(300);
    digitalWrite(statusLEDPin, LOW);
    delay(1000);
  }
}
