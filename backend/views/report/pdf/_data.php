<div>
	<table id="tbl_1" style="width: 100%;border-collapse:collapse; border-left: 1px solid #000;">
		<thead>
			<tr style="border: 1px solid #000; background: #d9d9d9;">
				<?php 

				foreach ($cols as $col) {
					?>
					<th style="font-size: 11px;border: 1px solid #000; font-size: 9px; padding: 4px">
						<?php echo isset($col['label']) ? $col['label'] : $col['attribute'];?>
					</th>
					<?php
				}

				?>
			</tr>
		</thead>
		<tbody>
			<?php
			$n = 0;
			foreach ( $data as $element ) {
				$n++;
				?>
				<tr>
					<?php
						foreach ($cols as $col) {
							?>
						<td style="
						background-color: <?php echo $n % 2 === 0 ? '#d5eafc' : '#9abfdf';?>;
						font-size: 11px;border: 1px dotted #000; font-size: 9px; padding: 4px">
							<?php 
							try {
								echo !empty($col['value']) ? $col['value']($element) : $element[$col['attribute']];
							} catch(\Exception $e){
								echo "";
							}
							?>
						</td>
						<?php
						}
				?>
				</tr>
				<?php
				if(isset($element['children']) && !empty($element['children']))
				{
					foreach ( $element['children'] as $_element ) {
						$n++;
						?>
						<tr>
							<?php
								foreach ($cols as $col) {
									?>
								<td style="
								background-color: <?php echo $n % 2 === 0 ? '#d5eafc' : '#9abfdf';?>;
								font-size: 11px;border: 1px dotted #000; font-size: 9px; padding: 4px">
									<?php 
									try {
										echo !empty($col['value']) ? $col['value']($_element) : $_element[$col['attribute']];
									} catch(\Exception $e){
										echo "";
									}
									?>
								</td>
								<?php
								}
						?>
						</tr>
						<?php
					}
				}
			}
			?>
		</tbody>
	</table>

</div>
