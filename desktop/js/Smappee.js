
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
    $('#md_modal').dialog({title: "{{Mon Appareil : }}" + $(this).attr('data-name')});
    $('#md_modal').load('index.php?v=d&plugin=Smappee&modal=appliance&name='
        + encodeURI($(this).attr('data-name'))
        + '&applianceId='
        + $(this).attr('data-appliance-id')).dialog('open');
});

$('a[data-action="save"].btn-success').off().on('click', function() {
    var id = $('.id').val();
    var appliance_name = $('.applianceName').val();
    var parent_object = $('#sel_object').val();
    var monitor_consumption = $('.monitorConsumption').is(':checked') ? "true" : "false";

    $.ajax({
        type: "POST",
        url: "plugins/Smappee/core/ajax/Smappee.ajax.php",
        data: {
            action: "applianceSave",
            type: "remote",
            id: id,
            appliance_name: appliance_name,
            parent_object: parent_object,
            monitor_consumption: monitor_consumption
        },
        dataType: 'json',
        error: function (request, status, error) {
            handleAjaxError(request, status, error);
        },
        success: function (data) {
            if (data.state != 'ok') {
                $('#div_alert').showAlert({message: data.result, level: 'danger'});
                return;
            } else {
                $('#md_modal').dialog('close');
                location.reload();
            }
        }
    });
});