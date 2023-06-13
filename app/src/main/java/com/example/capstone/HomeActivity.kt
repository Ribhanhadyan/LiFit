package com.example.capstone

import android.content.Intent
import androidx.appcompat.app.AppCompatActivity
import android.os.Bundle
import android.view.View
import android.widget.Button
import android.widget.ImageView
import android.widget.TextView

class HomeActivity : AppCompatActivity(), View.OnClickListener {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_home)

        val textViewBMIText : TextView = findViewById(R.id.tv_bmi_text)
        val imgvCamera : ImageView = findViewById(R.id.imgv_camera)
        val btnTest : Button = findViewById(R.id.btn_test)
        btnTest.setOnClickListener(this)
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.btn_test -> {
                val moveTest = Intent(this@HomeActivity, ResultActivity::class.java)
                startActivity(moveTest)
            }
        }

    }
}