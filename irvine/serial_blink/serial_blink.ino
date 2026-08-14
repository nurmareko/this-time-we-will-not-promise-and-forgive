void setup() {
  pinMode(LED_BUILTIN, OUTPUT);
  digitalWrite(LED_BUILTIN, LOW);
  Serial.begin(9600);
}

void loop() {
  char c = Serial.read();
  if (c == '1') {
    digitalWrite(LED_BUILTIN, HIGH);
  } else if (c == '0') {
    digitalWrite(LED_BUILTIN, LOW);
  }
}
