#!/usr/bin/env python3

import io
import json
import pytz
import smappy
import tempfile

def main():
    s = smappy.Smappee('AlienQueen', 'pniT248yJ1')
    s.authenticate('AlienQueen', 'Fassbinder#33')
    locs = s.get_service_locations()
    infos = locs['serviceLocations'][0]
    locs = infos['serviceLocationId']
    all = s.get_service_location_info(locs)
    appliances = all['appliances']

    dirpath = tempfile.gettempdir()

    with io.open(dirpath + '/Smappee.json', 'w', encoding='utf-8') as file:
        file.write(json.dumps(appliances, ensure_ascii=False))

    #for appliance in appliances:
    #    print('name: %s' % appliance['name'])
    #    print('id: %s' % appliance['id'])


if __name__ == '__main__':
    main()
