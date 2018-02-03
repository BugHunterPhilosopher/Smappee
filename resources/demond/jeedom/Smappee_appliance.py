#!/usr/bin/env python3

import pandas
import smappy
import sys

def main():
    # Authenticate to Smappee
    s = smappy.Smappee(sys.argv[1], sys.argv[2])
    s.authenticate(sys.argv[3], sys.argv[4])

    # calculate time frame
    start = pandas.to_datetime('now').tz_localize('UTC') + pandas.Timedelta(minutes=-5)
    end = pandas.to_datetime('now').tz_localize('UTC')

    # Retrieve global electric consumption
    locs = s.get_service_locations()
    loc = locs['serviceLocations'][0]
    id = loc['serviceLocationId']
    #print(s.get_service_location_info(id))

    consumption = s.get_events(service_location_id=id, start=start, end=end, max_number=1, appliance_id=sys.argv[5])
    #print(consumption)

    if (len(consumption) > 0):
        print(consumption[0]['activePower'])
        print(consumption[0]['totalPower'])
    else:
        print(0)
        print(0)


if __name__ == '__main__':
    main()