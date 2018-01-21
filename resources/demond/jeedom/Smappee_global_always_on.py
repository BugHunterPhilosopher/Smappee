#!/usr/bin/env python3

import datetime
import io
import pandas
import pytz
import smappy
import sys
import time

def main():
    # Authenticate to Smappee
    s = smappy.Smappee(sys.argv[1], sys.argv[2])
    s.authenticate(sys.argv[3], sys.argv[4])

    # calculate time frame
    start = pandas.to_datetime('now').tz_localize('Europe/Paris') + pandas.Timedelta(minutes=-5)
    end = pandas.to_datetime('now').tz_localize('Europe/Paris')

    # Retrieve global electric consumption
    locs = s.get_service_locations()
    loc = locs['serviceLocations'][0]
    id = loc['serviceLocationId']

    consumptions = s.get_consumption(id, start, end, 1)
    output = consumptions['consumptions'][0]['alwaysOn']
    print(output)


if __name__ == '__main__':
    main()