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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>
<form class="form-horizontal">
    <fieldset>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Server Path}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control server_path" data-l1key="server_path" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Client ID}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control client_id" data-l1key="client_id" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Client Secret}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control client_secret" data-l1key="client_secret" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Nom d'utilisateur}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control username" data-l1key="username" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-4 control-label">{{Mot de passe}}</label>
            <div class="col-lg-2">
                <input class="configKey form-control password" data-l1key="password" />
            </div>
        </div>
  </fieldset>
</form>

<script>
    function Smappee_postSaveConfiguration(){
        var server_path = $('.server_path').val();
        var client_id = $('.client_id').val();
        var client_secret = $('.client_secret').val();
        var username = $('.username').val();
        var password = $('.password').val();

        $.ajax({
            type: "POST",
            url: "plugins/Smappee/core/ajax/Smappee.ajax.php",
            data: {
                action: "postSave",
                type: "remote",
                server_path: server_path,
                client_id: client_id,
                client_secret: client_secret,
                username: username,
                password: password
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
                }
            }
        });
    }
</script>