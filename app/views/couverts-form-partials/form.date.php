<div class="row">
    <div class="column column-75">
        <h4>Reserveren bij <?php echo $info->RestaurantName; ?></h4>
        <p><?php echo $info->RestaurantPhoneNumber; ?> - <?php echo $info->RestaurantCity; ?></p>
        <p><?php echo $info->IntroText->$lang; ?></p>
        <?php if (isset($availableTimes)): ?>
            <p><strong><?php echo $availableTimes->NoTimesAvailable->Message->$lang; ?></strong></p>
        <?php endif; ?>
    </div>
    <div class="column column-25">
        <p><small>Stap 1 van 3</small></p>
    </div>
</div>
<form>
	<div class="row">
        <input type="hidden" name="currentStep" class="couvertsInput" value="1">
        <div class="column column-50">
            <label for="couvertsDate">Kies een datum</label>
            <input type="text" name="date" id="couvertsDate" class="couvertsInput" value="<?php echo date('d-m-Y'); ?>">
        </div>
        <div class="column column-50">
            <label for="couvertsNumberPersons">Aantal personen</label>
            <input type="number" id="couvertsNumberPersons" class="couvertsInput" name="numberPersons" value="2">   
        </div>
    </div>
	<div class="row">
        <button type="button" id="nextStep">Volgende</button>   
    </div>
</form>
