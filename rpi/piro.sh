#!/bin/bash
echo "Postavljam GPIO pinove u rezim izlaza..."
gpio mode 2 out && gpio write 2 1
gpio mode 0 out && gpio write 0 1
gpio mode 1 out && gpio write 1 1
gpio mode 4 out && gpio write 4 1
gpio mode 5 out && gpio write 5 1
gpio mode 6 out && gpio write 6 1
echo "Svi GPIO pinovi su namesteni!"



