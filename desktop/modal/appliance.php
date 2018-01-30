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

if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}

$eqLogic = eqLogic::byType('SmappeeAppliance');
$eqLogic = current($eqLogic);
$eqLogic = is_bool($eqLogic) ? NULL : $eqLogic;

require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
?>

<div class="col-lg-10 col-md-9 col-sm-8 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;">
    <a class="btn btn-success eqLogicAction pull-right" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
    <a class="btn btn-danger eqLogicAction pull-right" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
        <li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-tachometer"></i> {{Equipement}}</a></li>
    </ul>
    <div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
        <div role="tabpanel" class="tab-pane active" id="eqlogictab">
            <br/>
            <form class="form-horizontal">
                <fieldset>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{Nom de l'équipement Smappee}}</label>
                        <div class="col-sm-3">
                            <?php
                                echo '<input type="text" class="eqLogicAttr form-control applianceName" data-l1key="name" placeholder="' .
                                    $_GET['name'] . '" value="' . ((!is_null($eqLogic) && $eqLogic->getName() != 'Smappee') ? $eqLogic->getName() : $_GET['name']) . '"/>';
                                echo '<input type="hidden" class="eqLogicAttr form-control id" data-l1key="name" 
                                    value="' . $_GET['applianceId'] . '"/>';
                            echo '<input type="hidden" class="eqLogicAttr form-control oldName" data-l1key="name" 
                                    value="' . $_GET['name'] . '"/>';
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" >{{Objet parent}}</label>
                        <div class="col-sm-3">
                            <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                                <option value="">{{Aucun}}</option>
                                <?php
                                foreach (object::all() as $object) {
                                    $is_selected = (!is_null($eqLogic) &&
                                        is_object($eqLogic->getObject()) &&
                                        $eqLogic->getObject()->getId() == $object->getId()) ?
                                        'selected="selected"' : '';
                                    echo '<option value="' . $object->getId() . '" '. $is_selected . '>' . $object->getName() . '</option>';
                                }

                                $command = escapeshellcmd('../resources/demond/jeedom/Smappee_gather_appliances.py');
                                $output = shell_exec($command);
                                echo $output;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{Catégorie}}</label>
                        <div class="col-sm-9">
                            <?php
                            foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                                echo '<label class="checkbox-inline">';
                                echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                                echo '</label>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"></label>
                        <div class="col-sm-9">
                            <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" checked/>{{Activer}}</label>
                            <label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" checked/>{{Visible}}</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">{{Surveiller la consommation électrique}}</label>
                        <div class="col-sm-3">
                            <?php
                                echo '<input type="checkbox" class="eqLogicAttr form-control monitorConsumption" data-l1key="monitorConsumption"' .
                                    (!is_null($eqLogic) && $eqLogic->getConfiguration('monitor_consumption') === 'true' ? ' checked' : '') .
                                    '/>';
                            ?>
                        </div>
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
</div>
<?php include_file('desktop', 'Smappee', 'js', 'Smappee');?>