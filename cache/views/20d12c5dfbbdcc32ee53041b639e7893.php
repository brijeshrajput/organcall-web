<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Organ Call</title>
    <?php echo $debugbarRenderer->renderHead() ?>
</head>
<body>
    <h1>Welcome to Organ Call Application</h1>
    <p>Organ Call is a simple application that allows you to call organs from the database.</p>
    <?php if(isset($name)): ?>
        <h1>Welcome, <?php echo e($name); ?></h1>
    <?php endif; ?>
    <?php if(isset($data)): ?>
        <ul>
            <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>ID: <?php echo e($item['id']); ?>, Name: <?php echo e($item['name']); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    <?php endif; ?>
    
    <?php echo $debugbarRenderer->render() ?>
</body>
</html>
<?php /**PATH C:\laragon\www\organcall\src\Views/welcome.blade.php ENDPATH**/ ?>