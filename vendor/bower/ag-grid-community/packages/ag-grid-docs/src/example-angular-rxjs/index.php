<?php
$pageTitle = "ag-Grid Examples: Angular Components with RxJS";
$pageDescription = "ag-Grid is a feature-rich datagrid available in Free or Enterprise versions. This page shows examples of using ag-Grid with Angular and RxJS";
$pageKeyboards = "ag-Grid angular rxjs";
$pageGroup = "examples";
// define('skipInPageNav', true);
include '../documentation-main/documentation_header.php';
?>
    <h1>Angular Component using RxJs Example</h1>

    <h2>With updated rows only supplied to the grid.</h2>

    <p>A simple example where the grid receives the initial data from a subscription, and updates via another.</p>

    <p>In this example the grid only receives the updated rows and uses the <a href="../javascript-grid-data-update">Transaction</a> method
        of row updates.</p>

    <?= example('RxJS - With updated rows only supplied to the grid', 'rxjs-row', 'angular', array("exampleHeight" => 435, "showResult" => true)); ?>

    <h2>With full data set supplied to the grid, with changed data within.</h2>

    <p>A simple example where the grid receives the initial data from a subscription, and updates via another.</p>

    <p>In this example the grid only receives the the full row data via the 2nd subscription but makes uses of the <a href="../javascript-grid-data-update">deltaRowDataMode</a> method
        of row updates for improved performance.</p>

    <?= example('RxJS - With full data set supplied to the grid', 'rxjs-bulk', 'angular', array("exampleHeight" => 435, "showResult" => true)); ?>
<h2>
    Angular Grid Resources
</h2>
<br/>
<ul>
    <li>
        Get started with Angular Grid in 5 minutes in our <a href="../angular-getting-started/" target="_blank">guide</a>.
    </li>
    <br/>
    <li>
        Browse our <a href="../best-angular-2-data-grid/" target="_blank">Angular Grid</a> page to discover all major benefits in using ag-Grid Angular. 
    </li>
    <br/>
    <li>
        Please take a look at the <a href="../angular-more-details">more details</a> section next for more detailed information on using Angular with ag-Grid. 
    </li>
</ul>


<?php include '../documentation-main/documentation_footer.php';?>
