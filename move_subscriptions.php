<?php include('./includes/header.php');?>

<h1>Subscription Migration</h1>

<?php

require_once('./lib/Stripe.php');
require_once('./stripe_keys.php');

// Source Account to grab existing subscriptions
Stripe::setApiKey(SOURCE_KEY);

// Get existing customers from source account to display and process in HTML table below
$subscribers = array();
$count = 100;
while ($count == 100) {

	$criteria = array('limit' => 100);
	if (isset($starting_after)) $criteria['starting_after'] = $starting_after; // Pagination

	$customers = Stripe_Customer::all($criteria);

	// Prepare the customer subscription data that we need for migration
	foreach ($customers['data'] as $c) {

		// Loop if they have multiple subscriptions
		foreach ($c['subscriptions']['data'] as $s) {

			// Account info
			$subscribers[$s['id']]['customer_id'] = $c['id'];
			$subscribers[$s['id']]['description'] = $c['description'];
				
			// Subscription info
			$subscribers[$s['id']]['subscription_id'] = $s['id'];
			$subscribers[$s['id']]['plan_id'] = $s['plan']['id'];
			$subscribers[$s['id']]['billing_cycle_anchor'] = $s['current_period_end']; // ! Using existing end period as new billing_cycle_anchor

		}

		// Pagination
		$starting_after = $c['id'];
		$count = count($customers['data']);

	}

}

// MIGRATION CLASS
class stripe_migration {

	// Create new and cancel existing subscription
	function move_subscription($customer_id, $subscription_id, $plan_id, $billing_cycle_anchor) {

		try {

			// Remove OLD subscription on source account at end of cycle
			Stripe::setApiKey(SOURCE_KEY);

			$sub_cancel = Stripe_Customer::retrieve($customer_id);
			$sub_cancel->subscriptions->retrieve($subscription_id)->cancel(array('at_period_end' => TRUE));

			// Setup NEW subscription on destination account to begin billing next cycle via billing_cycle_anchor
			Stripe::setApiKey(DESTINATION_KEY);

			$sub_mig = Stripe_Customer::retrieve($customer_id);
			$response = $sub_mig->subscriptions->create(
				array(
					"plan" => $plan_id,
					"billing_cycle_anchor" => $billing_cycle_anchor,
					"prorate" => FALSE
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
			<th>Customer ID</th>
			<th>Subscription ID</th>
			<th>Plan ID</th>
			<th>Customer Description</th>
			<th>Billing Cycle Anchor</th>
			<th>MIGRATION RESULT</th>
		</tr>
	</thead>

	<tbody>
	<?php
		// Run through existing plans from source account
		foreach ($subscribers as $s) {
	?>

			<tr>
				<td><?=$s['customer_id'];?></td>
				<td><?=$s['subscription_id'];?></td>
				<td><?=$s['plan_id'];?></td>
				<td><?=$s['description'];?></td>
				<td><?=date('Y-m-d H:i:s A', $s['billing_cycle_anchor']);?></td>
				<td>
					<?php

						// Call function from above to create the plan into destination account
						$sm = new stripe_migration;
						echo $sm->move_subscription(
							$s['customer_id'],
							$s['subscription_id'],
							$s['plan_id'],
							$s['billing_cycle_anchor']
						);

					?>
				</td>
			</tr>

	<?php } ?>

	</tbody>

</table>

<?php include('./includes/footer.php');?>
