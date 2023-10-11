<?php

use Palax\App\App;
use Palax\TableInitializer;

require_once '../util/autoload.php';

const TYPE = 'employee_load';

$departments = App::getBitrixClient()->getDepartments();
$users = App::getBitrixClient()->getUsers();

TableInitializer::ShowHead();
?>

    <div id="name">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                    <form id="filter"
                          action="<?= App::getConfig()['excel'] ?>"
                          target="_blank"
                          method="POST"
                          data-action="<?= App::getConfig()['filter'] ?>"
                    >
                        <input type="hidden" class="form-control" required id="type" name="type" value="<?= TYPE ?>">
                        <div class="form-group">
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="date_from">От</label>
                                    <input type="date" class="form-control" placeholder="От" required name="date_from"
                                           id="date_from" value="<?= date('Y-m-d', strtotime('-1 week')) ?>">
                                </div>
                                <div class="col">
                                    <label for="date_to">до</label>
                                    <input type="date" class="form-control" placeholder="До" required name="date_to"
                                           id="date_to" value="<?= date('Y-m-d') ?>">
                                </div>
                                <?php if ($users) { ?>
                                    <div class="col">
                                        <label for="users">Подразделение</label>
                                        <select id="users" name="users[]"
                                                class="js-example-theme-multiple js-states form-control"
                                                data-tags="true" multiple="multiple">
                                            <?php foreach ($departments as $department) { ?>
                                                <optgroup label="<?= $department['NAME'] ?>">
                                                    <?php foreach ($users as $user) { ?>
                                                        <?php if (in_array($department['ID'], $user['UF_DEPARTMENT'])) { ?>
                                                            <option value="<?= $user['ID'] ?>">
                                                                <?= $user['LAST_NAME'] . ' ' . $user['NAME'] ?>
                                                            </option>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </optgroup>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php } ?>
                            </div>
                            <button id="filter_btn" type="submit" class="btn btn-primary btn-lg btn-save">применить
                            </button>
                            <button id="filter_excel" type="submit" style="display: none;"
                                    class="btn btn-primary btn-lg btn-save">скачать таблицу
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div id="reports">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th scope="col" class="sortable" data-sort="user.fio">Сотрудник</th>
                    <th scope="col" class="sortable" data-sort="in.count">Входящие</th>
                    <th scope="col" class="sortable" data-sort="out.count">Исходящие</th>
                    <th scope="col" class="sortable" data-sort="failed_count">Пропущенные</th>
                    <th scope="col" class="sortable" data-sort="count">Общее число звонков</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(".js-example-theme-multiple").select2({
            theme: "classic"
        });
        BX24.init(function () {
            let data = [];
            let direction = 1;

            $(document).on('click', '#filter_btn', function (e) {
                e.preventDefault();

                $(this).addClass('disabled');

                const type = $('#type').val();
                const date_from = $('#date_from').val();
                const date_to = $('#date_to').val();
                const users = $('#users').val();

                $.ajax({
                    url: $('#filter').data('action'),
                    type: 'post',
                    dataType: "json",
                    data: {
                        'type': type,
                        'date_from': date_from,
                        'date_to': date_to,
                        'users': users,
                    },
                    success: function (result) {
                        data = result;
                        showTable(result)
                        $("#filter_excel").show();
                    },
                    complete: () => {
                        $(this).removeClass('disabled');
                    }
                })
            });

            $(document).on('click', '.sortable', function () {
                const fieldName = $(this).data('sort').split('.')
                const getEl = (el, i = 0) => {
                    if (i === fieldName.length - 1) {
                        return el[fieldName[i]]
                    }

                    return getEl(el[fieldName[i++]], i)
                }

                const compare = (a, b) => {
                    a = getEl(a)
                    b = getEl(b)

                    if (a < b) {
                        return -1 * direction;
                    }
                    if (a > b) {
                        return 1 * direction;
                    }

                    return 0;
                }

                data.sort(compare)

                showTable(data)

                direction *= -1
            })


            function showTable(result) {
                const body = $('#reports').find('table tbody').eq(0);

                body.html('');

                for (const employee of result) {
                    body.append(`
                     <tr>
                      <th scope="row" title="${employee.user.id}">${employee.user.fio ? employee.user.fio : 'id: ' + employee.user.id}</th>
                      <td>${employee.in.count}</td>
                      <td>${employee.out.count}</td>
                      <td>${employee.failed_count}</td>
                      <td>${employee.count}</td>
                    </tr>
                    `);
                }
            }
        });
    </script>

<?php
TableInitializer::showEnd();



