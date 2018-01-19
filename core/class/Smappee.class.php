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

    public static function cron($_eqlogic_id = null)
    {
        log::add('Smappee', 'debug', 'Cron pull for Smappee1');

        if ($_eqlogic_id !== null) {
            log::add('Smappee', 'debug', 'Cron pull for Smappee2');
            $eqLogics = array(eqLogic::byId($_eqlogic_id));
            log::add('Smappee', 'debug', 'Cron pull for Smappee3');
        } else {
            log::add('Smappee', 'debug', 'Cron pull for Smappee4');
            $eqLogics = eqLogic::byType('Smappee');
            log::add('Smappee', 'debug', 'Cron pull for Smappee5');
            if ($eqLogics == null) {
                log::add('Smappee', 'debug', 'Cron pull for Smappee9');
                self::createEquipment();
                log::add('Smappee', 'debug', 'Cron pull for Smappee10');
                $eqLogics = eqLogic::byType('Smappee');
                log::add('Smappee', 'debug', 'Cron pull for Smappee11');
            }
        }

        log::add('Smappee', 'debug', 'Cron pull for SmappeeA');
        foreach ($eqLogics as $MySmappee) {
            log::add('Smappee', 'debug', 'Cron pull for SmappeeB');
            if ($MySmappee->getIsEnable() == 1) {
                log::add('Smappee', 'debug', 'Cron pull for SmappeeC');
                $global_electricity_consumption = rand(5, 15);
                log::add('Smappee', 'debug', 'Cron pull for SmappeeD');
                foreach ($MySmappee->getCmd('info') as $cmd) {
                    log::add('Smappee', 'debug', 'Cron pull for SmappeeE');
                    switch ($cmd->getName()) {
                        case 'Consommation électrique globale':
                            log::add('Smappee', 'debug', 'Cron pull for SmappeeF');
                            $value = $global_electricity_consumption;
                            log::add('Smappee', 'debug', 'Cron pull for SmappeeG');
                            break;
                    }

                    log::add('Smappee', 'debug', 'Cron pull for SmappeeH');
                    $cmd->event($value);
                    log::add('Smappee', 'debug', 'Cron pull for SmappeeI');
                    log::add('Smappee','debug',"set '".$cmd->getName()."' to ". $value . "W");
                    log::add('Smappee', 'debug', 'Cron pull for SmappeeJ');
                }

                log::add('Smappee', 'debug', 'Cron pull for SmappeeK');
                $MySmappee->refreshWidget();
                log::add('Smappee', 'debug', 'Cron pull for SmappeeL');
            }
        }
    }

    private static function createEquipment()
    {
        self::$Smappee = new eqLogic();
        log::add('Smappee', 'debug', 'Cron pull for Smappee10');
        self::$Smappee->setEqType_name('Smappee');
        self::$Smappee->setIsEnable(1);
        self::$Smappee->setIsVisible(1);
        self::$Smappee->setStatus('OK');
        self::$Smappee->setName('Consommation électrique globale');
        log::add('Smappee', 'debug', 'Cron pull for Smappee11');
        self::$Smappee->setLogicalId(uniqid());
        log::add('Smappee', 'debug', 'Cron pull for Smappee12');
        self::$Smappee->save();
        log::add('Smappee', 'debug', 'Cron pull for Smappee13');
        Smappee::createCommand(self::$Smappee->getId());
        log::add('Smappee', 'debug', 'Cron pull for Smappee14');
    }

    public static function createCommand($id) {
        log::add('Smappee','debug','Execution du preUpdate()');

        //Rajout des commandes
        $SmappeeCmd = new SmappeeCmd();
        log::add('Smappee', 'debug', 'Cron pull for Smappee300');
        $SmappeeCmd->setName('Consommation électrique globale');
        log::add('Smappee', 'debug', 'Cron pull for Smappee400');
        $SmappeeCmd->setLogicalId('Consommation électrique globale');
        log::add('Smappee', 'debug', 'Cron pull for Smappee500');
        $SmappeeCmd->setEqLogic_id($id);
        //$SmappeeCmd->preSave();
        log::add('Smappee', 'debug', 'Cron pull for Smappee600');
        $SmappeeCmd->setUnite('W');
        $SmappeeCmd->setType('info');
        log::add('Smappee', 'debug', 'Cron pull for Smappee700');
        $SmappeeCmd->setEventOnly(1);
        log::add('Smappee', 'debug', 'Cron pull for Smappee800');
        $SmappeeCmd->setConfiguration('onlyChangeEvent', 1);
        log::add('Smappee', 'debug', 'Cron pull for Smappee900');
        $SmappeeCmd->setIsHistorized(1);
        log::add('Smappee', 'debug', 'Cron pull for Smappee1000');
        $SmappeeCmd->setSubType('numeric');
        log::add('Smappee', 'debug', 'Cron pull for Smappee1100');
        $SmappeeCmd->save();

        log::add('Smappee','debug','Fin execution du preUpdate()');
        return $SmappeeCmd;
    }

    public function postUpdate() {
        $this->cron();
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

    public function execute($_options =null) {
        if ($this->getType() == '') {
            return '';
        }
        $eqLogic = $this->getEqlogic();
        $eqLogic->cron($eqLogic->getId());
    }
}
