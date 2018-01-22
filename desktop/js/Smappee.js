
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

$('#bt_globalConsumption').show();
$('#bt_globalConsumption').off().on('click', function () {
    $('#md_modal').dialog({title: "{{Consommation Globale}}"});
    $('#md_modal').load('index.php?v=d&plugin=Smappee&modal=global.consumption').dialog('open');
});


$('.eqLogicDisplayCard').off().on('click', function() {
    $('#md_modal').dialog({title: "{{Mon Appareil}}"});
    $('#md_modal').load('index.php?v=d&plugin=Smappee&modal=appliance&id=' + $(this).attr('data-logical-id')).dialog('open');
});