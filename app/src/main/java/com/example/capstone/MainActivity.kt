package com.example.capstone

import android.content.Intent
import androidx.appcompat.app.AppCompatActivity
import android.os.Bundle
import android.view.View
import android.widget.Button
import android.widget.EditText

class MainActivity : AppCompatActivity(), View.OnClickListener {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        val editTextEmail: EditText = findViewById(R.id.et_email)
        val editTextPassword: EditText = findViewById(R.id.et_password)

        val btnLogin: Button = findViewById(R.id.btn_login)
        btnLogin.setOnClickListener(this)

        val btnRegister: Button = findViewById(R.id.btn_register)
        btnRegister.setOnClickListener(this)
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.btn_login -> {
                val moveLogin = Intent(this@MainActivity, HomeActivity::class.java)
                startActivity(moveLogin)
                }
        }
        when (v?.id) {
            R.id.btn_register -> {
                val moveRegister = Intent(this@MainActivity, RegisterActivity::class.java)
                startActivity(moveRegister)
            }
        }
    }
}