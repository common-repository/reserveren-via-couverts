<div class="row">
	<div class="column column-100">
		<h4><?php echo $info->RestaurantName; ?></h4>
		<p><?php echo $info->RestaurantPhoneNumber; ?> - <?php echo $info->RestaurantCity; ?></p>
	</div>
</div>
<hr>
<form>
	<input type="hidden" name="currentStep" value="<?php echo ($formData['currentStep'] + 1); ?>">
	<input type="hidden" name="date" value="<?php echo $date->format('d-m-Y') ?>">
	<input type="hidden" name="timeSelected" value="">
	<input type="hidden" name="numberPersons" value="<?php echo intval($formData['numberPersons']); ?>">

	<div class="row">
		<div class="column column-50">
			<p>Datum: <?php echo $date->format('d-m-Y'); ?></p>
		</div>
		<div class="column column-50">
			<p>Aantal personen: <?php echo intval($formData['numberPersons']); ?></p>
		</div>
	</div>
	
	<div class="row">
		<h5>Beschikbare tijden:</h5>
		<div id="timeHolder">
			<?php $i = 0; ?>
			<?php foreach ($availableTimes->Times as $time): ?>
				<?php $i++; ?>
				<a href="#" class="couvertsTimeSelect <?php if ($i % 2 == 0): ?>even<?php else: ?>odd<?php endif; ?>"><?php echo $time->Hours.':'.$time->Minutes; if ($time->Minutes == 0) { echo 0; }?> </a>
		    <?php endforeach; ?>
		</div>
	</div>
	<div class="row">
		<button type="button" id="prevStep" class="hidden">Vorige</button>
		<button type="button" id="nextStep" class="hidden">Volgende</button>
	</div>
</form>
