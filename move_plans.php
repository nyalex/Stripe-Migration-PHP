<?php include('./includes/header.php');?>

<h1>Plan Migration</h1>

<?php

require_once('./lib/Stripe.php');
require_once('./stripe_keys.php');

// SOURCE ACCOUNT
Stripe::setApiKey(SOURCE_KEY);

// Get existing plans from source account
$plans = Stripe_Plan::all(array('limit' => 100));
$plans = (array) $plans['data'];

class stripe_migration {

	// CREATE PLAN FUNCTION
	function create_plan($id, $name, $amount, $currency, $interval, $interval_count, $trial_period_days) {

		try {

			// Create plan on destination account
			Stripe::setApiKey(DESTINATION_KEY);

			$response = Stripe_Plan::create(
				array(
					"id" => $id,
					"name" => $name,
					"amount" => $amount,
					"currency" => $currency,
					"interval" => $interval,
					"interval_count" => $interval_count,
					"trial_period_days" => $trial_period_days,
					"statement_description" => "Refersion"
				)
			);

			if (json_decode($response) !== NULL) {
				return '<span class="green bold">SUCCESS!</span>';
			}

		} catch (Exception $e) {
			return '<span class="red bold">ERROR: </span> ' .  $e->getMessage();
		}

	}

}

?>

<table border="1" class="table-striped">
	<thead>
		<tr>
			<th>Plan ID</th>
			<th>Name</th>
			<th>Amount</th>
			<th>Currency</th>
			<th>Interval</th>
			<th>Interval Count</th>
			<th>Trial Period Days</th>
			<th>MIGRATION RESULT</th>
		</tr>
	</thead>

	<tbody>
	<?php
		// Run through existing plans from source account
		foreach ($plans as $p) {
	?>

			<tr>
				<td><?=$p['id'];?></td>
				<td><?=$p['name'];?></td>
				<td><?=$p['amount'];?></td>
				<td><?=$p['currency'];?></td>
				<td><?=$p['interval'];?></td>
				<td><?=$p['interval_count'];?></td>
				<td><?=($p['trial_period_days'] == NULL ? 0 : $p['trial_period_days']);?></td>
				<td>
					<?php

						// Call function from above to create the plan into destination account
						$sm = new stripe_migration;
						echo $sm->create_plan(
							$p['id'],
							$p['name'],
							$p['amount'],
							$p['currency'],
							$p['interval'],
							$p['interval_count'],
							$p['trial_period_days']
						);

					?>
				</td>
			</tr>

	<?php } ?>

	</tbody>

</table>

<?php include('./includes/footer.php');?>
