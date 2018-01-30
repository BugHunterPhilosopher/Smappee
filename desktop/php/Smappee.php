<?php
    if (!isConnect('admin')) {
        throw new Exception('{{401 - Accès non autorisé}}');
    }

    $plugin = plugin::byId('Smappee');
    sendVarToJS('eqType', $plugin->getId());
    $eqLogics = eqLogic::byType($plugin->getId());

require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
?>

<?php include_file('desktop', 'Smappee', 'css', 'Smappee'); ?>

<div class="row row-overflow">
    <div class="col-lg-2 col-md-3 col-sm-4">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter un Smappee}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
                foreach ($eqLogics as $eqLogic) {
                    $opacity = ($eqLogic->getIsEnable()) ? '' : jeedom::getConfiguration('eqLogic:style:noactive');
                    echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getLogicalId() . '" style="' . $opacity .'"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>

    <div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
        <legend>{{Mes Smappees}}</legend>
        <legend><i class="fa fa-cog"></i>  {{Gestion}}</legend>
        <div class="eqLogicThumbnailContainer">
            <div class="cursor eqLogicAction" data-action="add" style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >
                <i class="fa fa-plus-circle" style="font-size : 6em;color:#94ca02;"></i>
                <br>
                <span style="font-size : 1.1em;position:relative; top : 23px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#94ca02">{{Ajouter}}</span>
            </div>
            <div class="cursor eqLogicAction" data-action="gotoPluginConf" style="text-align: center; background-color : #ffffff; height : 120px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;">
                <i class="fa fa-wrench" style="font-size : 6em;color:#767676;"></i>
                <br>
                <span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#767676">{{Configuration}}</span>
                <br>
        </span>
            </div>
        </div>
        <legend><i class="fa fa-table"></i> {{Mes Appareils}}</legend>
        <div class="eqLogicThumbnailContainer">
            <?php
            // Retrieve appliances data
            $tempdir = sys_get_temp_dir();
            $json = file_get_contents($tempdir . '/Smappee.json');
            $json_data = json_decode($json, true);
            $opacity = '' ;

            $eqLogics = eqLogic::byType('SmappeeAppliance');

            // Display appliances
            foreach ($json_data as $appliance) {
                $found = false;

                foreach ($eqLogics as $appliance_in_db) {
                    if (explode('||', $appliance_in_db->getLogicalId())[1] == $appliance['id']) {
                        $appliance_name = explode('||', $appliance_in_db->getLogicalId())[0];
                        $found = true;
                    }
                }

                if (!$found) {
                    $appliance_name = (empty($appliance['name'])) ? $appliance['id'] : $appliance['name'];
                }

                echo '<span class="eqLogicDisplayCard cursor smappeeAppliance" data-name="' . $appliance_name .
                    '" data-appliance-id="' . $appliance['id'] . '">';
                echo '<img class="smappeeApplianceImage" src="' . $plugin->getPathImgIcon() . '" height="140" width="130" />';
                echo '<span style="font-size : 1.1em;position:relative; top : 15px;">' . $appliance_name . '</span>';
                echo '</span>';
            }
            ?>
        </div>
    </div>
</div>

<?php include_file('desktop', 'Smappee', 'js', 'Smappee');?>
