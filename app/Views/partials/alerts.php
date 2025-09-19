<?php
$session = session();
if ($session->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc($session->getFlashdata('error')) ?></div>
<?php endif; ?>

<?php if ($session->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc($session->getFlashdata('success')) ?></div>
<?php endif; ?>

<?php if (isset($errors) && is_array($errors) && count($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
