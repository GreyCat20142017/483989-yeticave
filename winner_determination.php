<?php

    require_once('functions.php');
    require_once('./vendor/autoload.php');

    $winners = create_and_get_winners_list($connection);

    if ($winners && count($winners) > 0) {
        try {

            $transport = new Swift_SmtpTransport('phpdemo.ru', 25);
            $transport->setUsername('keks@phpdemo.ru');
            $transport->setPassword('htmlacademy');

            foreach ($winners as $winner) {

                $body_content = include_template('email.php', [
                    'winner' => $winner
                ]);

                $message = new Swift_Message("Ваша ставка победила");
                $type = $message->getHeaders()->get('Content-Type');
                $type->setValue('text/html');
                $type->setParameter('charset', 'utf-8');
                /**
                 * Здесь должно бы быть get_assoc_element($winner, 'email'), но вдруг "левые" адреса все-таки существуют.
                 * Поэтому... так...
                 */
                $message->setTo([TEST_EMAIL => get_assoc_element($winner, 'username')]);
                $message->setBody(get_assoc_element($winner, 'name'));
                $message->addPart($body_content, 'text/html');
                $message->setFrom("keks@phpdemo.ru", "YetiCave");
                $mailer = new Swift_Mailer($transport);
                $mailer->send($message);

            }

        } catch (Exception $e) {

        }

        header('Location: index.php');
    }