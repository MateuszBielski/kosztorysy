#!/bin/bash

ad1="http://norma:8001/cl/table/"
ad2="/1"
for i in {11000..11100}
do
   curl -o -I -L -s -w "%{http_code}" $ad1$i$ad2
   echo . $i
done