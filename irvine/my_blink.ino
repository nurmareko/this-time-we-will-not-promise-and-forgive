const int WAIT_FAST = 500;
const int WAIT_SLOW = 2000;
int loop_counter = 0;
bool is_fast = true;

void setup() {
  pinMode(LED_BUILTIN, OUTPUT);
}

void blinkLed(int wait_time) {
  digitalWrite(LED_BUILTIN, HIGH);
  delay(wait_time);
  digitalWrite(LED_BUILTIN, LOW);
  delay(wait_time);
}

void loop() {
  blinkLed(is_fast ? WAIT_FAST: WAIT_SLOW);
  if ((loop_counter = (loop_counter + 1) % 5) == 0) {
      is_fast = !is_fast;
  }
}
