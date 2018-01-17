#!/usr/bin/env python3

import io
import json
import pytz
import smappy
import sys
import tempfile
import yaml

def main():
    dirpath = tempfile.gettempdir()

    # Authenticate to Smappee
    s = smappy.Smappee(sys.argv[1], sys.argv[2])
    s.authenticate(sys.argv[3], sys.argv[4])

    # Retrieve appliances
    locs = s.get_service_locations()
    infos = locs['serviceLocations'][0]
    locs = infos['serviceLocationId']
    all = s.get_service_location_info(locs)
    appliances = all['appliances']

    # Write appliances to disk, in order to be processed by PHP
    with io.open(dirpath + '/Smappee.json', 'w', encoding='utf-8') as file:
        file.write(json.dumps(appliances, ensure_ascii=False))


if __name__ == '__main__':
    main()
