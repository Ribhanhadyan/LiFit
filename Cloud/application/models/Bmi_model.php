<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Bmi_model extends CI_Model
{
    public function save_bmi_record($data)
    {
        $record = [
            'weight' => $data['weight'],
            'height' => $data['height'],
            'bmi' => $data['bmi'],
            'result' => $data['result']
        ];
        
        $this->db->insert('bmi_records', $record);
        
        return $this->db->insert_id();
    }

    public function get_meal_plan($bmiCategory)
    {
        // Define meal plans for different BMI categories
        $mealPlans = [
            'Underweight' => 'Eat a balanced diet with a slightly higher caloric intake to gain weight.',
            'Normal weight' => 'Maintain a balanced diet with a moderate caloric intake to maintain weight.',
            'Overweight' => 'Follow a calorie-restricted diet with a focus on nutrient-dense foods to lose weight.',
            'Obesity' => 'Consult a healthcare professional for a personalized meal plan and guidance.',
        ];

        // Return the meal plan based on the BMI category
        return isset($mealPlans[$bmiCategory]) ? $mealPlans[$bmiCategory] : 'No meal plan available.';
    }

    public function get_exercise_suggestions($bmiCategory)
    {
        // Define exercise suggestions for different BMI categories
        $exerciseSuggestions = [
            'Underweight' => 'Incorporate strength training exercises to build muscle mass.',
            'Normal weight' => 'Maintain a regular exercise routine with a mix of cardio and strength training.',
            'Overweight' => 'Focus on aerobic exercises like walking, jogging, or cycling for weight loss.',
            'Obesity' => 'Consult a healthcare professional for personalized exercise recommendations and guidance.',
        ];

        // Return the exercise suggestions based on the BMI category
        return isset($exerciseSuggestions[$bmiCategory]) ? $exerciseSuggestions[$bmiCategory] : 'No exercise suggestions available.';
    }
}
