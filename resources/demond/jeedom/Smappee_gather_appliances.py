#!/usr/bin/env python3

import io
import json
import pytz
import smappy
import sys
import tempfile

def main():
    dirpath = tempfile.gettempdir()
    #with io.open(dirpath + '/Smappee.log', 'w', encoding='utf-8') as file:
    #    file.write(u"1: " + sys.argv[1] + ", 2: " +  sys.argv[2] + ", 3: " + sys.argv[3] + ", 4: " + sys.argv[4])

    # Authenticate to Smappee
    s = smappy.Smappee(sys.argv[1], sys.argv[2])
    s.authenticate(sys.argv[3], sys.argv[4])

    # Retrieve appliances
    locs = s.get_service_locations()
    loc = locs['serviceLocations'][0]
    id = loc['serviceLocationId']
    all = s.get_service_location_info(id)
    appliances = all['appliances']

    # Write appliances to disk, in order to be processed by PHP
    with io.open(dirpath + '/Smappee.json', 'w', encoding='utf-8') as file:
        file.write(json.dumps(appliances, ensure_ascii=False))


if __name__ == '__main__':
    main()
