<?php
defined('BASEPATH') or exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Bmi extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Bmi_model');
    }

    // POST /api/bmi
    public function index_post()
    {
        $data = $this->post();

        // Validate the input data
        $this->form_validation->set_data($data);
        $this->form_validation->set_rules('weight', 'Weight', 'required|numeric');
        $this->form_validation->set_rules('height', 'Height', 'required|numeric');

        if ($this->form_validation->run() === false) {
            $this->response(['error' => validation_errors()], 400);
        } else {
            // Calculate BMI
            $weight = $data['weight'];
            $height = $data['height'];
            $bmi = $this->calculate_bmi($weight, $height);

            // Prepare the response data
            $response = [
                'weight' => $weight,
                'height' => $height,
                'bmi' => $bmi,
                'result' => $this->get_bmi_result($bmi)
            ];

            // Save the BMI record in the database
            $recordId = $this->Bmi_model->save_bmi_record($response);
            $response['record_id'] = $recordId;

            $this->response(200);
        }
    }

    // Calculate BMI
    private function calculate_bmi($weight, $height)
    {
        // Perform the BMI calculation based on the provided weight and height
        $height_in_meters = $height / 100; // Convert height to meters
        $bmi = $weight / ($height_in_meters * $height_in_meters);
        return round($bmi, 2); // Round the BMI to two decimal places
    }

    // Get BMI result category
    private function get_bmi_result($bmi)
    {
        // Define the BMI result categories and their ranges
        $categories = [
            ['category' => 'Underweight', 'min' => 0, 'max' => 18.4],
            ['category' => 'Normal weight', 'min' => 18.5, 'max' => 24.9],
            ['category' => 'Overweight', 'min' => 25, 'max' => 29.9],
            ['category' => 'Obesity', 'min' => 30, 'max' => 100],
        ];

        // Determine the BMI result based on the calculated BMI
        foreach ($categories as $category) {
            if ($bmi >= $category['min'] && $bmi <= $category['max']) {
                return $category['category'];
            }
        }

        return 'Unknown';
    }

        // GET /api/bmi/meal-plan/{id}
        public function meal_plan_get($id)
        {
            $bmiRecord = $this->Bmi_model->get_bmi_record($id);
    
            if (!$bmiRecord) {
                $this->response(['error' => 'BMI record not found.'], 404);
            } else {
                $bmiCategory = $bmiRecord['result'];
                $mealPlan = $this->Bmi_model->get_meal_plan($bmiCategory);
    
                $response = [
                    'bmi_category' => $bmiCategory,
                    'meal_plan' => $mealPlan,
                ];
    
                $this->response(201);
            }
        }
    
        // GET /api/bmi/exercise-suggestions/{id}
        public function exercise_suggestions_get($id)
        {
            $bmiRecord = $this->Bmi_model->get_bmi_record($id);
    
            if (!$bmiRecord) {
                $this->response(['error' => 'BMI record not found.'], 404);
            } else {
                $bmiCategory = $bmiRecord['result'];
                $exerciseSuggestions = $this->Bmi_model->get_exercise_suggestions($bmiCategory);
    
                $response = [
                    'bmi_category' => $bmiCategory,
                    'exercise_suggestions' => $exerciseSuggestions,
                ];
    
                $this->response(200);
            }
        }
    
}
