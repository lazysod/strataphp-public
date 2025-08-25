<?php
// Example API controller for /api/v1/jokes endpoints
require_once __DIR__ . '/ApiController.php';
require_once __DIR__ . '/ApiHelper.php';
class JokesApiController extends ApiController
{
    public function index()
    {
    $this->error('Bad Request', 400);
    }

    // GET /api/v1/jokes/random
    public function random()
    {
        $jokes = [
            ["id" => 1, "joke" => "Why did the chicken cross the road? To get to the other side!"],
            ["id" => 2, "joke" => "I told my computer I needed a break, and it said 'No problem, I'll go to sleep.'"],
            ["id" => 3, "joke" => "Why do programmers prefer dark mode? Because light attracts bugs!"],
        ];
        $random = $jokes[array_rand($jokes)];
        $this->json($random);
    }

    // GET /api/v1/jokes/{id}
    public function get($id)
    {
        // Validate parameter (example)
        $missing = ApiHelper::requireParams(['id' => $id], ['id']);
        if ($missing) {
            $this->json(ApiHelper::error('Missing parameter: ' . implode(', ', $missing), 400), 400);
            return;
        }

        $jokes = [
            1 => "Why did the chicken cross the road? To get to the other side!",
            2 => "I told my computer I needed a break, and it said 'No problem, I'll go to sleep.'",
            3 => "Why do programmers prefer dark mode? Because light attracts bugs!",
        ];

        if (isset($jokes[$id])) {
            $this->json(ApiHelper::success(['id' => $id, 'joke' => $jokes[$id]]));
        } else {
            $this->json(ApiHelper::error('Joke not found', 404), 404);
        }
    }
}
