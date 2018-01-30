<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    require_once dirname(__FILE__) . '/../class/Smappee.class.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect('admin')) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

    ajax::init();

    if (init('action') == 'postSave') {
        $type = init('type');

        if ($type == 'remote') {
            $client_id = init('client_id');
            $client_secret = init('client_secret');
            $username = init('username');
            $password = init('password');

            config::save('client_id', $client_id, 'Smappee');
            config::save('client_secret', $client_secret, 'Smappee');
            config::save('username' , $username, 'Smappee');
            config::save('password', $password, 'Smappee');

            passthru("python ../../resources/demond/jeedom/Smappee_gather_appliances.py "
                . config::byKey('client_id', 'Smappee') . " "
                . config::byKey('client_secret', 'Smappee') . " "
                . config::byKey('username', 'Smappee') . " "
                . config::byKey('password', 'Smappee'));

            Smappee::createEquipment();
        }
        ajax::success();
    }

    if (init('action') == 'applianceSave') {
        $type = init('type');

        if ($type == 'remote') {
            $id = init('id');
            $appliance_name = init('appliance_name');
            $old_name = init('old_name');
            $parent_object = init('parent_object');
            $monitor_consumption = init('monitor_consumption');

            $eqTypeName = 'Smappee';
            $eqLogics = eqLogic::byLogicalId($old_name . '||' . $id, "SmappeeAppliance");
            $is_not_empty = !empty(array_filter($eqLogics));
            log::add('Smappee', 'debug', 'appliance: ' . $eqTypeName . ', found?: ' . $is_not_empty);

            if ($is_not_empty) {
                array_pop($eqLogics)->remove();
                log::add('Smappee', 'debug', 'appliance: ' . $eqTypeName . ' removed from DB');
            }

            $eqLogic =  new eqLogic();
            $eqLogic->setName("Smappee - " . $appliance_name);
            $eqLogic->setEqType_name("SmappeeAppliance");
            $eqLogic->setObject_id((int)$parent_object);
            $eqLogic->setIsEnable(1);
            $eqLogic->setIsVisible(1);
            $eqLogic->setLogicalId($appliance_name . '||' . $id);
            $eqLogic->setConfiguration('monitor_consumption', $monitor_consumption);

            $eqLogic->save();
            log::add('Smappee', 'debug', 'appliance: ' . $eqTypeName . ' saved in DB');

            Smappee::createCommands($eqLogic->getId(),
                $appliance_name . ' - Consommation active',
                $appliance_name . ' - Consommation totale');
        }
    }
    ajax::success();

    throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}

