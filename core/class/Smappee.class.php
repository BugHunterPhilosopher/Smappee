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

    private static $Smappee;

    public static function cron5($_eqlogic_id = null)
    {
        log::add('Smappee', 'debug', 'Cron for Smappee');

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
                    . config::byKey('server_path', 'Smappee')
                    . "/plugins/Smappee/resources/demond/jeedom/Smappee_global_consumption.py "
                    . config::byKey('client_id', 'Smappee') . " "
                    . config::byKey('client_secret', 'Smappee') . " "
                    . config::byKey('username', 'Smappee') . " "
                    . config::byKey('password', 'Smappee'), $global_electricity_consumption);

                foreach ($MySmappee->getCmd('info') as $cmd) {
                    switch ($cmd->getName()) {
                        case 'Consommation électrique globale':
                            $value = $global_electricity_consumption[0];
                            break;
                    }

                    $cmd->event($value);
                    log::add('Smappee','debug',"set '".$cmd->getName()."' to ". $value . "W");
                }

                $MySmappee->refreshWidget();
            }
        }

        log::add('Smappee', 'debug', 'done Cron for Smappee');
    }

    private static function createEquipment()
    {
        self::$Smappee = new eqLogic();

        self::$Smappee->setEqType_name('Smappee');
        self::$Smappee->setIsEnable(1);
        self::$Smappee->setIsVisible(1);
        self::$Smappee->setStatus('OK');
        self::$Smappee->setName('Smappee');
        self::$Smappee->setLogicalId(uniqid());
        self::$Smappee->save();

        Smappee::createCommand(self::$Smappee->getId());
    }

    public static function createCommand($id) {
        $SmappeeCmd = new SmappeeCmd();

        $SmappeeCmd->setName('Consommation électrique globale');
        $SmappeeCmd->setLogicalId('Smappee');
        $SmappeeCmd->setEqLogic_id($id);
        $SmappeeCmd->setUnite('W');
        $SmappeeCmd->setType('info');
        $SmappeeCmd->setEventOnly(1);
        $SmappeeCmd->setConfiguration('onlyChangeEvent', 1);
        $SmappeeCmd->setIsHistorized(1);
        $SmappeeCmd->setSubType('numeric');
        $SmappeeCmd->save();
    }

    public function postUpdate() {
        $this->cron5();
    }
}

class SmappeeCmd extends cmd {

    public function preSave() {
        if ($this->getConfiguration('instance') === '') {
            $this->setConfiguration('instance', '1');
        }
        if ($this->getConfiguration('index') === '') {
            $this->setConfiguration('index', '0');
        }
        if (strpos($this->getConfiguration('class'), '0x') !== false) {
            $this->setConfiguration('class', hexdec($this->getConfiguration('class')));
        }
        $this->setLogicalId($this->getConfiguration('instance') . '.' . $this->getConfiguration('class') . '.' . $this->getConfiguration('index'));
    }

    public function execute($_options = null) {
        if ($this->getType() == '') {
            return '';
        }

        $eqLogic = $this->getEqlogic();
        $eqLogic->cron5($eqLogic->getId());
    }
}
