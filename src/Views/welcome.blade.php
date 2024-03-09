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
    @if (isset($name))
        <h1>Welcome, {{ $name }}</h1>
    @endif
    @if (isset($data))
        <ul>
            @foreach ($data as $item)
                <li>ID: {{ $item['id'] }}, Name: {{ $item['name'] }}</li>
            @endforeach
        </ul>
    @endif
    
    <?php echo $debugbarRenderer->render() ?>
</body>
</html>
