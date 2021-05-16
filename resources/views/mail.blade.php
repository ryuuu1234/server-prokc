<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProKc</title>
</head>
<body>
    <h1>{{$details['title']}}</h1>
    
    <div>{{ html_entity_decode($details['body']) }}</div>

</body>
</html>