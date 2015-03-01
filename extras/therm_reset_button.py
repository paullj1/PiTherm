#!/usr/bin/env python
import time
import sys
import signal
import RPi.GPIO as io
from subprocess import call

RESET_BUTTON_PIN = 22

# Close GPIO and database connections
def cleanup(signal, frame):
	io.cleanup()
	sys.exit(0)

def setup_io():
	io.setmode(io.BCM)	
	io.setwarnings(False)
	io.setup(RESET_BUTTON_PIN, io.IN, pull_up_down=io.PUD_DOWN)



#  MAIN #
setup_io()
signal.signal(signal.SIGINT, cleanup)
while True:

	io.wait_for_edge(RESET_BUTTON_PIN, io.RISING)  # add rising edge detection on a channel
	call(["service", "pitherm", "restart"])
	time.sleep(1)

# end while

io.cleanup();
