package com.example.capstone

import androidx.appcompat.app.AppCompatActivity
import android.os.Bundle
import android.widget.TextView
import com.example.capstone.Classifier
import com.example.capstone.ClassifierSigmoid
class ResultActivity : AppCompatActivity() {
    private lateinit var tvResultBmi : TextView
    private lateinit var tvResultAge : TextView
    private lateinit var tvResultGender : TextView

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_result)

        tvResultBmi = findViewById(R.id.tv_result_classification)
        tvResultAge = findViewById(R.id.tv_result_age)
        tvResultGender = findViewById(R.id.tv_result_gender)


        val resultsGender = intent.getParcelableArrayExtra("resultGender")
        if (resultsGender != null) {
            val recognitions = resultsGender.mapNotNull { it as? ClassifierSigmoid.Recognition }
            // Lakukan sesuatu dengan array of Recognition yang diterima
            // Contoh: tampilkan ID, judul, dan confidence setiap objek Recognition
            for (recognition in recognitions) {
                val id = recognition.id
                val title = recognition.title
                val confidence = recognition.confidence

                tvResultGender.setText(title+" ("+confidence.toString()+"%)")
            }
        }

        val resultsBMI = intent.getParcelableArrayExtra("resultBMI")
        if (resultsBMI != null) {
            val recognitions = resultsBMI.mapNotNull { it as? Classifier.Recognition }
            // Lakukan sesuatu dengan array of Recognition yang diterima
            // Contoh: tampilkan ID, judul, dan confidence setiap objek Recognition
            for (recognition in recognitions) {
                val id = recognition.id
                val title = recognition.title
                val confidence = recognition.confidence

                tvResultBmi.setText(title+" ("+confidence.toString()+"%)")
            }
        }

        val resultsAge = intent.getParcelableArrayExtra("resultAge")
        if (resultsAge != null) {
            val recognitions = resultsAge.mapNotNull { it as? Classifier.Recognition }
            // Lakukan sesuatu dengan array of Recognition yang diterima
            // Contoh: tampilkan ID, judul, dan confidence setiap objek Recognition
            for (recognition in recognitions) {
                val id = recognition.id
                val title = recognition.title
                val confidence = recognition.confidence

                tvResultAge.setText(title+" ("+confidence.toString()+"%)")
            }
        }
    }
}