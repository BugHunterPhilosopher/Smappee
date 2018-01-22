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

    throw new Exception(__('Aucune méthode correspondante à : ', __FILE__) . init('action'));
    /*     * *********Catch exeption*************** */
} catch (Exception $e) {
    ajax::error(displayExeption($e), $e->getCode());
}

