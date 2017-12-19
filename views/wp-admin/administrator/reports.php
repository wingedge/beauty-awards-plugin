<?php

use Extanet\BeautyAwards\WPAdmin\Administrator\Reports;
use Alekhin\Geo\Countries;

$categories = Reports::entries_per_category();
$countries = Reports::entries_per_country();
$cnames = Countries::get_countries();
?>
<div id="box_contest_reports" class="wrap">
    <h1 class="wp-heading-inline">Contest Management</h1>
    <hr class="wp-header-end" />

    <table class="form-table reports-table-total">
        <tbody>
            <tr>
                <th scope="row">Total Number of Entries</th>
                <td class="col-report-value"><?php echo Reports::total_entries(); ?></td>
            </tr>
        </tbody>
    </table>

    <table class="reports-table">
        <tbody>
            <tr>
                <td class="col-report-divider">
                    <h2>Entries Per Category</h2>
                    <?php if (empty($categories)): ?>
                        <p>No entries found.</p>
                    <?php else: ?>
                        <?php foreach ($categories as $category): ?>
                            <table class="widefat striped report-table">
                                <thead>
                                    <tr>
                                        <th colspan="2">
                                            <strong><?php echo $category->name; ?></strong>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($category->subcategories as $subcategory): ?>
                                        <tr>
                                            <th scope="row"><?php echo $subcategory->name; ?></th>
                                            <td class="col-report-value"><?php echo $subcategory->count; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </td>
                <td class="col-report-divider">
                    <h2>Entries Per Country</h2>
                    <?php if (empty($countries)): ?>
                        <p>No entries found.</p>
                    <?php else: ?>
                        <table class="widefat striped report-table">
                            <tbody>
                                <?php foreach ($countries as $country): ?>
                                    <tr>
                                        <th scope="row"><?php echo $cnames[$country->shipping_country]; ?></th>
                                        <td class="col-report-value"><?php echo $country->count; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>

</div>
