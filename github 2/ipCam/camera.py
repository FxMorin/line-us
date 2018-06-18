import cv2
import os
import time

while True:
  vidcap = cv2.VideoCapture(1)
  success, image = vidcap.read()
  success = True
  if (success):
      cv2.imwrite(os.path.dirname(os.path.realpath(__file__))+"/frame.jpeg", image)     # save frame as MJPEG file
      success,image = vidcap.read()
