<?php

require 'config/db.php';

$name = $_POST['name'] ?? null;
$review = $_POST['review'] ?? null;
$note = $_POST['note'] ?? null;
$date = date('Y-m-d H:i:s') ?? null;
$errors = [];



if (!empty($_POST)) {
    

    if (empty($name)) {
        $errors['name'] = 'Votre nom est requis.';
    }
    if (empty($review)) {
        $errors['review'] = 'Votre commentaire est requis.';
    }
    if (empty($name)) {
        $errors['note'] = 'Votre note doit être comprise entre 1 et 5.';
    }



    if (empty($errors)) {
        $query = $db->prepare('INSERT INTO review (name, review, note, created_at)
        VALUES (:name, :review, :note, :created_at)');
        $query->execute([
            ':name' => $name,
            ':review' => $review,
            ':note' => $note,
            ':created_at' => $date
        ]);
    }
}

$query = $db->prepare('SELECT * FROM review');
$query->execute();
$notes = $query->fetchAll();

var_dump($notes);


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon compte</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
</head>

<body class="container mx-auto h-full flex items-center flex-col">
    

    <!-- Notre moyenne -->
    <div class="flex justify-center w-4/6 mb-5">
        <div class="block rounded-lg shadow-lg bg-white border border-gray-300 w-full">
            <div class="py-3 px-6 border-b border-gray-300">
                Notre moyenne :
            </div>
            <div class="p-5 flex justify-around items-center">

                <div class="text-center">
                    <h2 class="text-amber-400 text-2xl mb-2"><span>3.3</span> <span>/</span> 5</h4>
                    <span class="text-amber-400">&#9733;</span>
                    <h4 class="text-xl font-medium mt-2">4 avis</h4>
                </div>
                
                <div class="w-1/3 flex flex-col">
                <?php for ($i=1; $i < 6; $i++) { ?>
                        <div class="flex justify-center items-center mb-2 ">
                            <p><?= $i; ?> <span class="text-amber-400">&#9733;</span></p>
                            <div class="w-4/5 mx-1  bg-gray-200 h-5 rounded-xl ">
                                    <div class="bg-amber-300 h-5 rounded-xl" style="width: 25%"></div>
                            </div>
                            <p>(1)</p>
                        </div>
                        <?php } ?>
                    </div>
                
                <div class="flex flex-col items-center justify-center">
                    <h5 class="text-gray-900 text-xl font-medium mb-2">Laissez votre avis</h5>
                    <button type="button" class=" inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">Noter</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Publier un avis -->
    <div class="flex justify-center w-4/6">
        <div class="block rounded-lg shadow-lg bg-white border border-gray-300 w-full">
            <?php if (!empty($errors)) { ?>
                <div class="bg-red-300 p-5 rounded border border-red-800 text-red-800 my-4">
                    <?php foreach ($errors as $error) { ?>
                        <p><?= $error; ?></p>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="py-3 px-6 border-b border-gray-300">
                Publier un avis :
            </div>
            <div class="p-6 text-center">
                <form action="" method="post">

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                            Nom :
                        </label>
                        <input class="shadow appearance-none border rounded w-1/2 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name" name="name" type="text" placeholder="Votre nom">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="review">
                            Commentaire :
                        </label>
                        <textarea class="shadow appearance-none border rounded w-1/2 py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="review" name="review" placeholder="Votre commentaire"></textarea>
                    </div>

                    <div class="mb-6">
                            <label class="mr-2">Note :</label>
                            <?php for ($i=1; $i < 6; $i++) { ?>
                                <input type="radio" name="note" id="<?= $i ?>" class="rounded" value="<?= $i ?>">
                                <label class="mr-2" for="<?= $i ?>"><?= $i ?></label>
                           <?php } ?>
                    </div>
                    <button class=" inline-block px-6 py-2.5 bg-blue-600 text-white font-medium text-xs leading-tight rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out">Noter</button>
                </form>

            </div>
        </div>
    </div>

    <!-- Affichage des commentaires -->

        
        <?php foreach ($notes as $note) { ?>

            <div class="flex justify-center w-4/6 mt-5 mb-5">
                <div class="block rounded-lg shadow-lg bg-white border border-gray-300 w-full">
                    <div class="py-3 px-6 border-b border-gray-300">
                        <?= $note['name'] ?>
                    </div>
                    <div class="p-5 flex justify-around items-center">
                        <?= $note['review'] ?>
                    </div>
        
                    <div class="py-3 px-6 border-t border-gray-300 text-end">
                        <?= $note['created_at'] ?>
                    </div>
                </div>
            </div>

        <?php } ?>
   

    



</body>
</html>