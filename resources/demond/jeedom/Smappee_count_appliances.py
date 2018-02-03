#!/usr/bin/env python3

import io
import json
import smappy
import sys
import tempfile

def main():
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
    dirpath = tempfile.gettempdir()
    with io.open(dirpath + '/Smappee.json', 'w', encoding='utf-8') as file:
        file.write(json.dumps(appliances, ensure_ascii=False))

    print(len(appliances))


if __name__ == '__main__':
    main()
