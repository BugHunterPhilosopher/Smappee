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

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';

class Smappee extends eqLogic {

    private static $appliance;

    public static function cron5($_eqlogic_id = null)
    {
        log::add('Smappee', 'debug', 'Cron for Smappee');

        # global consumption and global always on
        if ($_eqlogic_id !== null) {
            $eqLogics = array(eqLogic::byId($_eqlogic_id));
        } else {
            $eqLogics = eqLogic::byType('Smappee');

            if ($eqLogics == null) {
                self::createEquipment();
                $eqLogics = eqLogic::byType('Smappee');
            }
        }

        foreach ($eqLogics as $MySmappee) {

            if ($MySmappee->getIsEnable() == 1) {
                exec("python3 "
                    . dirname(__FILE__)
                    . "/../../../../plugins/Smappee/resources/demond/jeedom/Smappee_global.py "
                    . config::byKey('client_id', 'Smappee') . " "
                    . config::byKey('client_secret', 'Smappee') . " "
                    . config::byKey('username', 'Smappee') . " "
                    . config::byKey('password', 'Smappee'), $global_values);

                foreach ($MySmappee->getCmd('info') as $cmd) {
                    switch ($cmd->getName()) {
                        case 'En veille global':
                            $value = $global_values[0];
                            break;
                        case 'Consommation électrique globale':
                            $value = $global_values[1];
                            break;
                    }

                    $cmd->event($value);
                    log::add('Smappee','debug',"set '".$cmd->getName()."' to ". $value . "W");
                }

            }
            $MySmappee->refreshWidget();
        }

        # appliance consumptions
        $eqLogics = eqLogic::byType('SmappeeAppliance');
        $is_not_empty = !empty(array_filter($eqLogics));

        if ($is_not_empty) {
            foreach ($eqLogics as $appliance) {
                if ($appliance->getIsEnable() == 1) {
                    exec("python3 "
                        . dirname(__FILE__)
                        . "/../../../../plugins/Smappee/resources/demond/jeedom/Smappee_appliance.py "
                        . config::byKey('client_id', 'Smappee') . " "
                        . config::byKey('client_secret', 'Smappee') . " "
                        . config::byKey('username', 'Smappee') . " "
                        . config::byKey('password', 'Smappee') . " "
                        . explode('||', $appliance->getLogicalId())[1], $global_values);

                    foreach ($appliance->getCmd('info') as $cmd) {
                        switch ($cmd->getName()) {
                            case $appliance->getName() . ' - Consommation active':
                                $value = $global_values[0];
                                break;
                            case $appliance->getName() . ' - Consommation totale':
                                $value = $global_values[1];
                                break;
                        }

                        $cmd->event($value);
                        log::add('Smappee', 'debug', "id: " . explode('||', $appliance->getLogicalId())[1] . ", set '" . $cmd->getName() . "' to " . $value . "W");
                    }
                    $appliance->refreshWidget();
                }
            }
        }

        log::add('Smappee', 'debug', 'done Cron for Smappee');
    }

    public static function createEquipment()
    {
        self::$appliance = new eqLogic();

        self::$appliance->setName('Smappee');
        self::$appliance->setEqType_name('Smappee');
        self::$appliance->setIsEnable(1);
        self::$appliance->setIsVisible(1);
        self::$appliance->setStatus('OK');
        self::$appliance->setLogicalId(uniqid());
        self::$appliance->save();

        Smappee::createCommands(self::$appliance->getId(),
            'Consommation électrique globale',
            'En veille global');
    }

    public static function createAppliance($appliance_name, $id)
    {
        self::$appliance = new eqLogic();

        self::$appliance->setName("Smappee - " . $appliance_name);
        self::$appliance->setEqType_name('SmappeeAppliance');
        self::$appliance->setIsEnable(1);
        self::$appliance->setIsVisible(1);
        self::$appliance->setLogicalId($appliance_name . '||' . $id);
        self::$appliance->setConfiguration('monitor_consumption', TRUE);
        self::$appliance->save();

        Smappee::createCommands(self::$appliance->getId(),
            'Consommation Active',
            'Consommation Totale');
    }

    public static function createCommands($id, $cmd1_name, $cmd2_name = null) {
        $SmappeeCmd1 = new SmappeeCmd();

        $SmappeeCmd1->setName($cmd1_name);
        $SmappeeCmd1->setLogicalId('Smappee');
        $SmappeeCmd1->setEqLogic_id($id);
        $SmappeeCmd1->setUnite('W');
        $SmappeeCmd1->setType('info');
        $SmappeeCmd1->setEventOnly(1);
        $SmappeeCmd1->setConfiguration('onlyChangeEvent', 1);
        $SmappeeCmd1->setIsHistorized(1);
        $SmappeeCmd1->setSubType('numeric');
        $SmappeeCmd1->setEqType('Smappee');
        $SmappeeCmd1->save();

        if (!is_null($cmd2_name)) {
            $SmappeeCmd2 = new SmappeeCmd();

            $SmappeeCmd2->setName($cmd2_name);
            $SmappeeCmd2->setLogicalId('Smappee');
            $SmappeeCmd2->setEqLogic_id($id);
            $SmappeeCmd2->setUnite('W');
            $SmappeeCmd2->setType('info');
            $SmappeeCmd2->setEventOnly(1);
            $SmappeeCmd2->setConfiguration('onlyChangeEvent', 1);
            $SmappeeCmd2->setIsHistorized(1);
            $SmappeeCmd2->setSubType('numeric');
            $SmappeeCmd2->setEqType('Smappee');
            $SmappeeCmd2->save();
        }
    }

    public function postUpdate() {
        $this->cron5();
    }
}

class SmappeeCmd extends cmd {

    public function preSave() {
    }

    public function execute($_options = null) {
        if ($this->getType() == '') {
            return '';
        }

        $eqLogic = $this->getEqlogic();
        $eqLogic->cron5($eqLogic->getId());
    }
}
