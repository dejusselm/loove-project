<?php

class Interaction
{
    private int $id;
    private InteractionType $type;
    private int $userId;
    private int $profileId;

    private function __construct(
        int $id,
        InteractionType $type,
        int $userId,
        int $profileId
    ) {
        $this->id = $id;
        $this->type = $type;
        $this->userId = $userId;
        $this->profileId = $profileId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getInteraction(): InteractionType
    {
        return $this->type;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getProfileId(): int
    {
        return $this->profileId;
    }

}