<?php
require_once 'Controller.php';
require_once ROOT_PATH . 'repositories/UserRepository.php';
require_once ROOT_PATH . 'repositories/HobbiesRepository.php';
require_once ROOT_PATH . 'models/User.php';
require_once 'GeoLocalisation.php';

class SearchController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->requireAuth();
    }

    public function index(): array
    {

        $allHobbies = $this->hobbiesRepo->all();
        $profiles = $this->searchRepo->search([], $this->userId);

        return [
            'allHobbies' => $allHobbies,
            'profiles' => $profiles
        ];
    }

    public function search(): array
    {
        $radiusMap = [
            'five' => 5,
            'ten' => 10,
            'twenty-five' => 25,
            'fifty' => 50
        ];

        $selectedRadius = $_POST['radius'] ?? '';
        $radius = $radiusMap[$selectedRadius] ?? 10;

        $filters = [
            'name' => $_POST['name'] ?? '',
            'genders' => $_POST['genders'] ?? [],
            'relation' => $_POST['relation'] ?? '',
            'hobbies' => $_POST['hobbies'] ?? [],
            'city' => $_POST['city'] ?? '',
            'radius' => $radius,
            'minAge' => $_POST['minAge'] ?? 18,
            'maxAge' => $_POST['maxAge'] ?? 100,

            'astrology' => '',
            'studies_level' => '',
            'job' => ''
        ];

        if ($this->user->isMember()) {
            $filters['astrology'] = $_POST['astrology'] ?? '';
            $filters['studies_level'] = $_POST['studies_level'] ?? '';
            $filters['job'] = trim($_POST['job'] ?? '');
        }

        $results = $this->searchRepo->search($filters, $this->userId);

        return $results;
    }




}
