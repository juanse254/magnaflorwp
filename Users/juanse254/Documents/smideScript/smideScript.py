import requests
import json
import daemon
import jsonify



class minuteClient:

    url = 'https://intern.smide.ch/api/v1/'
    refreshToken = ''
    token = ''
    userId = ''
    headerAuth = {'Authorization': 'Bearer ' + token, 'user-agent': 'okhttp/3.10.0'}
    headersimple = {'content-type': 'application/json'}

    def __init__(self):
        print('Im starting to populate')

    def login(self):
        email = 'tester123@getnada.com';
        password = 'martina2';
        headers = {'content-type': 'application/json'}
        body = {'email': email, 'loginType': "password", 'password': password}
        r = requests.post(self.url + 'user/login', data=json.dumps(body), headers=headers)
        dataRecived = r.json()
        self.token = dataRecived['token']
        self.refreshToken = dataRecived['refreshToken']
        self.userId = dataRecived['userId']
        self.headerAuth['Authorization'] = 'Bearer ' + self.token #Set the token for the first time.

    def user(self):
        r = requests.get(self.url + 'user', headers=self.headerAuth)
        return r.json()

    def getBikes(self):
        endpoint = 'bikes/all'

    def dropZones(self):
        endpont = 'dropOffZones'

    def booking(self):
        endpoint = 'booking'
        phoneLatitude = 0
        phoneLongitude = 0
        userId = ""
        bikeId = ""

    def bookingDetails(self):
        endpoint = 'booking'

    def bookingEnd(self):
        endpoint = 'booking/' + bookingId + '/end'

    def startDaemon(self):
        daemon.basic_daemonize()

x = minuteClient() #instanciate
x.login() #login
x.user() #validate
#getBikes
#getDropoffZones and use the bike id to find the best dropoff zone.
#book
#endbooking

print('done')