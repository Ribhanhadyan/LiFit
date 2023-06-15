package com.example.capstone

import android.content.Intent
import androidx.appcompat.app.AppCompatActivity
import android.os.Bundle
import android.view.View
import android.widget.Button
import android.widget.EditText

class RegisterActivity : AppCompatActivity(), View.OnClickListener {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_register)

        val editTextRegisterName: EditText = findViewById(R.id.et_register_name)
        val editTextRegisterEmail: EditText = findViewById(R.id.et_register_email)
        val editTextRegisterPassword: EditText = findViewById(R.id.et_register_password)

        val btnSignup: Button = findViewById(R.id.btn_signup)
        btnSignup.setOnClickListener(this)
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.btn_signup -> {
                val moveSignup = Intent(this@RegisterActivity, HomeActivity::class.java)
                startActivity(moveSignup)
            }
        }

    }
}