<?php

use Extanet\BeautyAwards\WPAdmin\Administrator\Contest;
use Extanet\BeautyAwards\Core\Contest as ContestSource;
?>
<div id="box_contest_manage" class="wrap">
    <h1 class="wp-heading-inline">Contest Management</h1>
    <hr class="wp-header-end" />

    <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
        <?php wp_nonce_field('manage_contest', 'session_marker'); ?>
        <h2>Contest/Judging Status</h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="ddl_status">Contest Status</label>
                    </th>
                    <td>
                        <?php $status = is_null(Contest::$p) ? ContestSource::status() : Contest::$p->data->status; ?>
                        <select id="ddl_status" name="status" class="widefat">
                            <option value="0">Closed - Judges can NOT judge entries</option>
                            <option value="1"<?php echo $status ? ' selected="selected"' : ''; ?>>Open - Judges can judge entries</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <h2>Front-end Settings</h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label>Show Countdown</label>
                    </th>
                    <td>
                        <div>
                            <label for="chk_countdown_opening">
                                <input type="checkbox" id="chk_countdown_opening" name="countdown_opening" value="1"<?php echo (is_null(Contest::$p) ? ContestSource::countdown_opening() : Contest::$p->data->countdown->opening) ? ' checked="checked"' : ''; ?> />
                                Opening
                            </label>
                        </div>
                        <div>
                            <label for="chk_countdown_closing">
                                <input type="checkbox" id="chk_countdown_closing" name="countdown_closing" value="1"<?php echo (is_null(Contest::$p) ? ContestSource::countdown_closing() : Contest::$p->data->countdown->closing) ? ' checked="checked"' : ''; ?> />
                                Ending
                            </label>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <h2>Entry Submission</h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label>Start Date</label>
                    </th>
                    <td>
                        <?php $start_date = ContestSource::start_date(); ?>
                        <?php $start_month = is_null(Contest::$p) ? ($start_date > 0 ? date('n', $start_date) : 0) : Contest::$p->data->date->start->month; ?>
                        <?php $start_day = is_null(Contest::$p) ? ($start_date > 0 ? date('j', $start_date) : 0) : Contest::$p->data->date->start->day; ?>
                        <?php $start_year = is_null(Contest::$p) ? ($start_date > 0 ? date('Y', $start_date) : 0) : Contest::$p->data->date->start->year; ?>
                        <select id="ddl_startdate_month" name="startdate_month">
                            <option value="0">(month)</option>
                            <option value="1"<?php echo ($start_month == 1) ? ' selected="selected"' : ''; ?>>January</option>
                            <option value="2"<?php echo ($start_month == 2) ? ' selected="selected"' : ''; ?>>February</option>
                            <option value="3"<?php echo ($start_month == 3) ? ' selected="selected"' : ''; ?>>March</option>
                            <option value="4"<?php echo ($start_month == 4) ? ' selected="selected"' : ''; ?>>April</option>
                            <option value="5"<?php echo ($start_month == 5) ? ' selected="selected"' : ''; ?>>May</option>
                            <option value="6"<?php echo ($start_month == 6) ? ' selected="selected"' : ''; ?>>June</option>
                            <option value="7"<?php echo ($start_month == 7) ? ' selected="selected"' : ''; ?>>July</option>
                            <option value="8"<?php echo ($start_month == 8) ? ' selected="selected"' : ''; ?>>August</option>
                            <option value="9"<?php echo ($start_month == 9) ? ' selected="selected"' : ''; ?>>September</option>
                            <option value="10"<?php echo ($start_month == 10) ? ' selected="selected"' : ''; ?>>October</option>
                            <option value="11"<?php echo ($start_month == 11) ? ' selected="selected"' : ''; ?>>November</option>
                            <option value="12"<?php echo ($start_month == 12) ? ' selected="selected"' : ''; ?>>December</option>
                        </select>
                        <input type="number" id="txt_startdate_day" name="startdate_day" min="0" max="31" value="<?php echo $start_day; ?>" placeholder="Day" />
                        <input type="number" id="txt_startdate_year" name="startdate_year" min="0" max="9999" value="<?php echo $start_year; ?>" placeholder="Year" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label>End Date</label>
                    </th>
                    <td>
                        <?php $end_date = ContestSource::end_date(); ?>
                        <?php $end_month = is_null(Contest::$p) ? ($end_date > 0 ? date('n', $end_date) : 0) : Contest::$p->data->date->end->month; ?>
                        <?php $end_day = is_null(Contest::$p) ? ($end_date > 0 ? date('j', $end_date) : 0) : Contest::$p->data->date->end->day; ?>
                        <?php $end_year = is_null(Contest::$p) ? ($end_date > 0 ? date('Y', $end_date) : 0) : Contest::$p->data->date->end->year; ?>
                        <select id="ddl_enddate_month" name="enddate_month">
                            <option value="0">(month)</option>
                            <option value="1"<?php echo ($end_month == 1) ? ' selected="selected"' : ''; ?>>January</option>
                            <option value="2"<?php echo ($end_month == 2) ? ' selected="selected"' : ''; ?>>February</option>
                            <option value="3"<?php echo ($end_month == 3) ? ' selected="selected"' : ''; ?>>March</option>
                            <option value="4"<?php echo ($end_month == 4) ? ' selected="selected"' : ''; ?>>April</option>
                            <option value="5"<?php echo ($end_month == 5) ? ' selected="selected"' : ''; ?>>May</option>
                            <option value="6"<?php echo ($end_month == 6) ? ' selected="selected"' : ''; ?>>June</option>
                            <option value="7"<?php echo ($end_month == 7) ? ' selected="selected"' : ''; ?>>July</option>
                            <option value="8"<?php echo ($end_month == 8) ? ' selected="selected"' : ''; ?>>August</option>
                            <option value="9"<?php echo ($end_month == 9) ? ' selected="selected"' : ''; ?>>September</option>
                            <option value="10"<?php echo ($end_month == 10) ? ' selected="selected"' : ''; ?>>October</option>
                            <option value="11"<?php echo ($end_month == 11) ? ' selected="selected"' : ''; ?>>November</option>
                            <option value="12"<?php echo ($end_month == 12) ? ' selected="selected"' : ''; ?>>December</option>
                        </select>
                        <input type="number" id="txt_enddate_day" name="enddate_day" min="0" max="31" value="<?php echo $end_day; ?>" placeholder="Day" />
                        <input type="number" id="txt_enddate_year" name="enddate_year" min="0" max="9999" value="<?php echo $end_year; ?>" placeholder="Year" />
                    </td>
                </tr>
            </tbody>
        </table>
        <h2>Payment Settings</h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="txt_entry_fee">Entry Fee (US$)</label>
                    </th>
                    <td>
                        <input type="number" id="txt_entry_fee" name="entry_fee" min="0" step="0.01" value="<?php echo (is_null(Contest::$p) ? ContestSource::entry_fee() : Contest::$p->data->entry_fee); ?>" placeholder="Entry Fee" />
                    </td>
                </tr>
            </tbody>
        </table>
        <p class="submit">
            <button id="btn_save_changes" name="save_changes" class="button-primary">
                Save Changes
            </button>
        </p>
    </form>
</div>
