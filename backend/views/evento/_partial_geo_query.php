<?php

use common\models\geo\GeoQuery;

$groups = [];

foreach ($geoQueries as $query) {
    if ($position == 'EVENTO' && $query['result_position'] == 0) {
        continue;
    }
    if ($position == 'TAB' && $query['result_position'] == 1) {
        continue;
    }

    if (!isset($groups[$query['group']])) {
        $groups[$query['group']] = ['queries'];
    }

    $groups[$query['group']]['queries'][] = $query;
}

foreach ($groups as $key => $value) {
    echo !empty($key) ? "<p><b>" . $key . "</b><br />" : "";
    ?>

		<table class="table table-bordered table-striped">
		<?php

        foreach ($value['queries'] as $query) {
            ?>
			<tr>
				<td colspan="3">
				<b><?php echo $query['name'];?></b>
				</td>
			</tr>
			<?php

            if (count($query['results']) > 0) {
                foreach ($query['results'] as $result) {
                    ?>
				<tr>
					<td style="width: 30%"><?php echo is_bool($result['main_result']) ? ($result['main_result'] ? 'Si' : 'No') : $result['main_result'];?></td>
					<td style="width: 10%"><?php echo (isset($result['distance_to_show'])) ? intval($result['distance_to_show'])." mt" : '';?></td>
					<td style="width: 60%"><?php echo isset($result['lon']) ? "LAT: ".substr($result['lat'], 0, 8)." LON: " . substr($result['lon'], 0, 8) . "<br />".$result['deg']." " : '';?></td>
					<?php /* <td><?php echo isset($result['lon']) ? "LAT: ".substr($result['lat'], 0, 8)." (".$result['s_lat'].")<br />LON: " . substr($result['lon'], 0, 8) . " (".$result['s_lon'].")<br />".$result['deg']." " : '';?></td> */?>
				</tr>
				<?php
                }
            }
        }
    ?>
		</table>
	</p>
	<?php
}
