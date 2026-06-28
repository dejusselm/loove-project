<?php
require_once ROOT_PATH . 'repositories/MembershipsRepository.php';
require_once ROOT_PATH . 'repositories/ViewsRepository.php';
require_once ROOT_PATH . 'repositories/UserRepository.php';
require_once ROOT_PATH . 'repositories/NotificationsRepository.php';

class User
{
    private int $id;
    private string $email;
    private string $password;
    public string $gender;
    public string $last_name;
    public DateTime $birthdate;
    public string $first_name;
    public string $role;
    public string $interest;
    public int $active;
    public string $avatar;
    public ?string $description;
    public ?string $relationship;
    public ?float $longitude;
    public ?float $latitude;
    public int $report_count = 0;
    public string $reasons = '';
    public ?int $age;
    public ?array $hobbiesList;
    public ?string $city;

    private ?string $features;

    public function __construct(
        int $id,
        string $email,
        string $password,
        string $gender,
        string $last_name,
        DateTime $birthdate,
        string $first_name,
        string $role,
        string $interest,
        int $active,
        string $avatar,
        ?string $description,
        ?string $relationship,
        ?float $longitude,
        ?float $latitude,
        ?string $city,
        ?string $features

    ) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->gender = $gender;
        $this->last_name = $last_name;
        $this->birthdate = $birthdate;
        $this->first_name = $first_name;
        $this->role = $role;
        $this->interest = $interest;
        $this->active = $active;
        $this->avatar = $avatar;
        $this->description = $description;
        $this->relationship = $relationship;
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->city = $city;
        $this->features = $features;

    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getActive(): int
    {
        return $this->active;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getAge(): int
    {
        $currentDate = new DateTime();
        $difference = $currentDate->diff($this->birthdate);
        return $difference->y;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }
    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function getAvatar(): string
    {
        return $this->avatar;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function getInterest(): string
    {
        return $this->interest;
    }
    public function getRelationship(): ?string
    {
        return $this->relationship;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function isMember(): bool
    {
        $memberRepo = new MembershipsRepository();
        return $memberRepo->isUserPremium($this->id);
    }

    public function getFeatures(): array
    {
        return $this->features ? json_decode($this->features, true) : [];
    }

    public function getTotalViews(): int
    {
        $viewsRepo = new ViewsRepository();
        return $viewsRepo->getTotalViews($this->id);
    }

    public function getProfileViewers(): array
    {
        $viewsRepo = new ViewsRepository();
        return $viewsRepo->getProfileViewers($this->id);
    }

    public function getStripeSubscriptionId(): ?string
    {
        $memberRepo = new MembershipsRepository();
        return $memberRepo->getUserStripeId($this->id);
    }

    public function getNotifications()
    {
        $notifRepo = new NotificationsRepository();
        return $notifRepo->getNonRead($this->id);
    }

    public function getPhotos(): array
    {
        $userRepo = new UserRepository();
        return $userRepo->getPhotos($this->id);
    }
}