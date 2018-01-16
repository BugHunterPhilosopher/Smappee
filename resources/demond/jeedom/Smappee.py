#!/usr/bin/env python3

import pytz
import smappy

def main():
    s = smappy.Smappee('AlienQueen', 'pniT248yJ1')
    s.authenticate('AlienQueen', 'Fassbinder#33')
    locs = s.get_service_locations()
    #print(locs)
    infos = locs['serviceLocations'][0]['serviceLocationId']
    #print(infos)
    all = s.get_service_location_info(infos)
    #print(all)
    appliances = all['appliances']
    #print("appliances: " + appliances)
    for appliance in appliances:
        #print(appliance)
        print('name: %s' % appliance['name'])
        print('id: %s' % appliance['id'])


if __name__ == '__main__':
    main()
