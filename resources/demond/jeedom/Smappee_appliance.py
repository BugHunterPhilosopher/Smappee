#!/usr/bin/env python3

import datetime
import smappy
import sys

def main():
    # Authenticate to Smappee
    s = smappy.Smappee(sys.argv[1], sys.argv[2])
    s.authenticate(sys.argv[3], sys.argv[4])

    # calculate time frame
    start = datetime.datetime.now() - datetime.timedelta(hours=1)
    end = datetime.datetime.now()

    # Retrieve global electric consumption
    locs = s.get_service_locations()
    loc = locs['serviceLocations'][0]
    id = loc['serviceLocationId']

    consumption = s.get_events(id, sys.argv[5], start, end)

    if (len(consumption) > 0):
        for p in range(0, len(consumption)):
            print(consumption[p]['activePower'])
            print(consumption[p]['totalPower'])
    else:
        print(0)
        print(0)

if __name__ == '__main__':
    main()