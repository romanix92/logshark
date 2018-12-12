#!/usr/bin/env python3

import urllib
from urllib.parse import urlencode, quote_plus
import http.client
import time
import sys
#from dptools.ssh import client
import threading
import argparse


class LogAggClient(object):
    
    def __init__(self, host):
        self.connection = http.client.HTTPConnection(host)
        self.api_path='/api';
        self.filename="default"
        self.async_logger = AsyncLogger(self)
    
    def post(self, data, **kwargs):
        req = urlencode(kwargs);
        headers = {"Content-type": "application/octet-stream", "Accept": "text/plain"}
        req_path = "{}?{}".format(self.api_path, req)
        #print(req_path)
        self.connection.request('POST', req_path+"", data, headers)
        return self.connection.getresponse().read()
    
    def get(self, **kwargs):
        req = urlencode(kwargs);
        headers = {}
        req_path = "{}?{}".format(self.api_path, req)
        self.connection.request('GET', req_path+"", "", headers)
        return self.connection.getresponse().read()
    
    def sync_log(self, text):
        self.post(text, action="log", file=self.filename)
        
    def log(self, text):
        self.async_logger.log(text)
    

class AsyncLogger(object):
    
    def __init__(self, client):
        self.client = client
        self.wt = threading.Thread(target=self.worker)
        self.enabled = False
        self.active = False
        self.buffer = ""
        self.lock = threading.Lock()

    def start(self):
        if not self.enabled:
            self.enabled = True
            self.wt = threading.Thread(target=self.worker)
            self.wt.start()

    def stop(self):
        if self.enabled:
            self.enabled = False
            self.wt.join(5000)

    def worker(self):
        self.active = True
        while self.enabled:
            time.sleep(1)
            with self.lock:
                if (len(self.buffer)):
                    self.client.sync_log(self.buffer)
                    self.buffer = ""
                else:
                    self.client.sync_log(self.buffer)
        self.enabled = False
        self.active = False
        
    def log(self, text):
        with self.lock:
            self.buffer+=text
        self.start()
    
    
        
            
parser = argparse.ArgumentParser(description='Log forwarder')
parser.add_argument('file', help='Filename to store logs')
args = parser.parse_args()
    
    
s = LogAggClient("localhost:8087")
s.filename = args.file;

try:
    for line in sys.stdin:
        s.log(line)
        sys.stdout.write(line)
finally:
    s.async_logger.stop()
