package com.example.capstone

import android.Manifest
import android.content.Intent
import android.content.pm.PackageManager
import android.graphics.Bitmap
import android.graphics.drawable.BitmapDrawable
import android.os.Bundle
import android.provider.MediaStore
import android.view.View
import android.widget.Button
import android.widget.ImageView
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.core.app.ActivityCompat
import com.example.capstone.Classifier
import com.example.capstone.ClassifierSigmoid

class HomeActivity : AppCompatActivity(), View.OnClickListener {
    private val mInputSize = 224
    private val mModelPathGender = "model_gender_8.tflite"
    private val mLabelPathGender = "label_gender.txt"
    private lateinit var classifierGender: ClassifierSigmoid

    private val mModelPathBMI = "model_bmi_9.tflite"
    private val mLabelPathBMI = "label_bmi.txt"
    private lateinit var classifierBMI: Classifier

    private val mModelPathAge = "model_age_4.tflite"
    private val mLabelPathAge = "label_age.txt"
    private lateinit var classifierAge: Classifier

    private lateinit var btnCamera: Button
    private lateinit var btnTest: Button
    private lateinit var imgvCamera: ImageView


    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_home)
        initClassifier()

        btnCamera = findViewById(R.id.btn_camera)
        btnTest = findViewById(R.id.btn_test)
        imgvCamera = findViewById(R.id.imgv_camera)

        btnCamera.isEnabled = true

        if (ActivityCompat.checkSelfPermission(
                this,
                Manifest.permission.CAMERA
            ) != PackageManager.PERMISSION_GRANTED
        ) {
            ActivityCompat.requestPermissions(this, arrayOf(Manifest.permission.CAMERA), 100)
        } else {
            btnCamera.isEnabled = true
        }

        btnCamera.setOnClickListener {
            val intent = Intent(MediaStore.ACTION_IMAGE_CAPTURE)
            startActivityForResult(intent, 101)
        }
        btnTest.setOnClickListener(this)
    }

    private fun initClassifier() {
        classifierGender = ClassifierSigmoid(assets, mModelPathGender, mLabelPathGender, mInputSize)
        classifierBMI = Classifier(assets, mModelPathBMI, mLabelPathBMI, mInputSize)
        classifierAge = Classifier(assets, mModelPathAge, mLabelPathAge, mInputSize)
    }

    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode == 101 && resultCode == RESULT_OK) {
            val imageBitmap = data?.extras?.get("data") as Bitmap?
            imgvCamera.setImageBitmap(imageBitmap)
        }
    }

    override fun onRequestPermissionsResult(
        requestCode: Int,
        permissions: Array<out String>,
        grantResults: IntArray
    ) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults)
        if (requestCode == 100 && grantResults.isNotEmpty() && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
            btnCamera.isEnabled = true
        }
    }

    override fun onClick(v: View?) {
        when (v?.id) {
            R.id.btn_test -> {
                val drawable = imgvCamera.drawable
                if (drawable is BitmapDrawable) {
                    val bitmap = drawable.bitmap
                    val resultGender = classifierGender.recognizeImage(bitmap)
                    val resultBMI = classifierBMI.recognizeImage(bitmap)
                    val resultAge = classifierAge.recognizeImage(bitmap)
                    /**runOnUiThread { Toast.makeText(this, resultGender.get(0).title, Toast.LENGTH_SHORT).show() }
                    **/
                    val intent = Intent(this@HomeActivity, ResultActivity::class.java)
                    intent.putExtra("resultBMI", resultBMI.toTypedArray())
                    intent.putExtra("resultAge", resultAge.toTypedArray())
                    intent.putExtra("resultGender", resultGender.toTypedArray())
                    startActivity(intent)
                }
            }
        }
    }
}
