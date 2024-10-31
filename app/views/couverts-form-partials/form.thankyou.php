<?php if (isset($response->ConfirmationText->$lang)): ?>

	<div class="row">
        <div class="column column-100">
             <h4><?php echo $response->ConfirmationText->$lang; ?></h4>   
        </div>
    </div>

<?php elseif (isset($response->ConfirmationText->English)): ?>

    <div class="row">
        <div class="column column-100">
        	<h4><?php echo $response->ConfirmationText->English; ?></h4>
        </div>
    </div>

<?php else: ?>

	<div class="row">
         <p><?php __('It looks like an error occured when processing your reservation.', 'tussendoorCouverts'); ?></p>   
    </div>
	<form>
		<input type="hidden" name="currentStep" value="<?php echo ($formData['currentStep'] + 1); ?>">
	    <input type="hidden" name="date" value="<?php echo $date->format('d-m-Y H:i') ?>">
	    <input type="hidden" name="numberPersons" value="<?php echo intval($formData['numberPersons']); ?>">
	    <div class="row">
            <button type="button" id="prevStep">Vorige</button>   
        </div>
	</form>

<?php endif; ?>
