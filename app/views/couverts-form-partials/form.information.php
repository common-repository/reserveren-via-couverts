<div class="row">
    <div class="column column-100">
        <h4><?php echo $info->RestaurantName; ?></h4>
        <p><?php echo $info->RestaurantPhoneNumber; ?> - <?php echo $info->RestaurantCity; ?></p>
    </div>
</div>
<hr>
<style type="text/css">
        label {
            display: block;
        }

        label.error {
            font-weight: bold;
            color: red;
        }

        input.error {
            border-color: red;
        }
</style>

<?php if (isset($errors['error']) && $errors['error'] > 0): ?>
    <p><strong>Er is tenminste een veld niet (goed) ingevuld.</strong></p>
<?php endif; ?>

<form>
    <input type="hidden" name="currentStep" value="<?php echo ($formData['currentStep'] + 1); ?>">
    <input type="hidden" name="date" value="<?php echo $date->format('d-m-Y H:i') ?>">
    <input type="hidden" name="numberPersons" value="<?php echo intval($formData['numberPersons']); ?>">

    <div class="row">
        <h5>Voer je gegevens in</h5>
        <p>Zodra je alle gegevens hebt ingevuld en op reserveren klikt leggen wij jouw reservering vast.</p>
    </div>

    <div class="row">
        <div class="column column-50">
            <p>Datum: <?php echo $date->format('d-m-Y H:i'); ?></p>
        </div>
        <div class="column column-50">
            <p>Aantal personen: <?php echo intval($formData['numberPersons']); ?></p>
        </div>
    </div>

    <div class="row">
        <?php if ($formSettings->Gender->Show): ?>
            <div class="column column-100">
                <label class="<?php if (isset($errors['fields']['Gender'])): ?> error <?php endif; ?>">
                    Geslacht<?php echo ($formSettings->Gender->Required ? '*' : '' ); ?>  
                </label>
                <input class="couvertsCheck" type="radio" name="Gender" value="Male">Man
                <input class="couvertsCheck" type="radio" name="Gender" value="Female">Vrouw
            </div>
        <?php endif; ?>

        <?php if ($formSettings->FirstName->Show): ?>
            <div class="column column-50">
                <label class="<?php if (isset($errors['fields']['FirstName'])): ?> error <?php endif; ?>">
                    Voornaam<?php echo ($formSettings->FirstName->Required ? '*' : '' ); ?>    
                </label>
                <input class="couvertsInput <?php if (isset($errors['fields']['FirstName'])): ?> error <?php endif; ?>" type="text" name="FirstName" value="" <?php echo ($formSettings->FirstName->Required ? 'required' : '' ); ?>>
            </div>
        <?php endif; ?>

        <?php if ($formSettings->LastName->Show): ?>
            <div class="column column-50">
                <label class="<?php if (isset($errors['fields']['LastName'])): ?> error <?php endif; ?>">
                    Achternaam<?php echo ($formSettings->LastName->Required ? '*' : '' ); ?></label>
                <input class="couvertsInput <?php if (isset($errors['fields']['LastName'])): ?> error <?php endif; ?>" type="text" name="LastName" value="" <?php echo ($formSettings->LastName->Required ? 'required' : '' ); ?>>
            </div>
        <?php endif; ?>

        <?php if ($formSettings->Email->Show): ?>
            <div class="column column-50">
                <label class="<?php if (isset($errors['fields']['Email'])): ?> error <?php endif; ?>">
                    E-mail<?php echo ($formSettings->Email->Required ? '*' : '' ); ?>    
                </label>
                <input class="couvertsInput <?php if (isset($errors['fields']['Email'])): ?> error <?php endif; ?>" type="text" name="Email" value="" <?php echo ($formSettings->Email->Required ? 'required' : '' ); ?>>
            </div>
        <?php endif; ?>

        <?php if ($formSettings->PhoneNumber->Show): ?>
            <div class="column column-50">
                <label class="<?php if (isset($errors['fields']['PhoneNumber'])): ?> error <?php endif; ?>">
                    Telefoonnummer<?php echo ($formSettings->PhoneNumber->Required ? '*' : '' ); ?>    
                </label>
                <input class="couvertsInput <?php if (isset($errors['fields']['PhoneNumber'])): ?> error <?php endif; ?>" type="text" name="PhoneNumber" value="" <?php echo ($formSettings->PhoneNumber->Required ? 'required' : '' ); ?>>
            </div>
        <?php endif; ?>

        <?php if ($formSettings->PostalCode->Show): ?>
            <div class="column column-50">
                <label class="<?php if (isset($errors['fields']['PostalCode'])): ?> error <?php endif; ?>">
                    Postcode<?php echo ($formSettings->PostalCode->Required ? '*' : '' ); ?>    
                </label>
                <input class="couvertsInput <?php if (isset($errors['fields']['PostalCode'])): ?> error <?php endif; ?>" type="text" name="PostalCode" value="" <?php echo ($formSettings->PostalCode->Required ? 'required' : '' ); ?>>
            </div>
        <?php endif; ?>

        <?php if ($formSettings->BirthDate->Show): ?>
            <div class="column column-50">
                <label class="<?php if (isset($errors['fields']['BirthDate'])): ?> error <?php endif; ?>">
                    Geboortedatum<?php echo ($formSettings->BirthDate->Required ? '*' : '' ); ?>    
                </label>
                <input class="couvertsInput <?php if (isset($errors['fields']['BirthDate'])): ?> error <?php endif; ?>" type="text" name="BirthDate" value="" <?php echo ($formSettings->BirthDate->Required ? 'required' : '' ); ?>>
            </div>
        <?php endif; ?>

        <?php if ($formSettings->Comments->Show): ?>
            <div class="column column-100">
                <label class="<?php if (isset($errors['fields']['Comments'])): ?> error <?php endif; ?>">
                    Opmerkingen<?php echo ($formSettings->Comments->Required ? '*' : '' ); ?></label>
                <textarea class="couvertsText <?php if (isset($errors['fields']['Comments'])): ?> error <?php endif; ?>" name="Comments" <?php echo ($formSettings->Comments->Required ? 'required' : '' ); ?>></textarea>
            </div>
        <?php endif; ?>


        <?php foreach ($formSettings->RestaurantSpecificFields as $extraFields): ?>
            <div class="column column-100">
                <label class="<?php if (isset($errors['fields'][$extraFields->Id])): ?> error <?php endif; ?>">
                    <?php echo $extraFields->Title->$lang; ?><?php echo ($extraFields->Required ? '*' : '' ); ?>
                </label>
                <?php if ($extraFields->Description->$lang != $extraFields->Title->$lang): ?>
                    <p><small><?php echo $extraFields->Description->$lang; ?></small></p>
                <?php endif; ?>
                <?php if ($extraFields->Type == 'Text'): ?>
                    <textarea class="<?php if (isset($errors['fields'][$extraFields->Id])): ?> error <?php endif; ?>" name="<?php echo $extraFields->Id; ?>" <?php echo ($extraFields->Required ? 'required' : '' ); ?>></textarea>
                <?php elseif ($extraFields->Type == 'Input'): ?>
                    <input class="couvertsInput <?php if (isset($errors['fields'][$extraFields->Id])): ?> error <?php endif; ?>" type="text" name="<?php echo $extraFields->Id; ?>" <?php echo ($extraFields->Required ? 'required' : '' ); ?>>
                <?php endif; ?>
            </div>

        <?php endforeach; ?>
    </div>

    <div class="row">
        <button type="button" id="prevStep">Vorige</button>
        <button type="button" id="nextStep">Reserveren</button>
    </div>
    
</form>
