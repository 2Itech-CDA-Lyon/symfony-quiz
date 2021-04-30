<?php

namespace App\Security\Voter;

use App\Entity\Quiz;
use App\Entity\Answer;
use App\Entity\Question;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class QuizVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['VIEW', 'EDIT'])
            && ($subject instanceof Quiz || $subject instanceof Question || $subject instanceof Answer);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if ($user !== 'anon.' && $user->hasRole('ROLE_ADMIN')) {
            return true;
        }

        switch (\get_class($subject)) {
            case Quiz::class:
                $quiz = $subject;
                break;
            case Question::class:
                $quiz = $subject->getQuiz();
                break;
            case Answer::class:
                $quiz = $subject->getQuestion()->getQuiz();
                break;
        }

        switch ($attribute) {
            case 'VIEW':
                return true;
            case 'EDIT':
                return $quiz->getAuthor() === $user;
            default:
                throw new \LogicException('This code should never be reached!');
        }
    }
}
