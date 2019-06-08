
private AsyncTask start;
private ProgressDialog dialog;

private void mulai() {
    start = new callAPI().execute();
    handler.postDelayed(new Runnable() {
    @Override
        public void run() {
            if (dialog.isShowing()) {
                dialog.dismiss();
                start.cancel(true);
                new android.support.v7.app.AlertDialog.Builder(v.getContext())
                    .setTitle("Informasi")
                    .setMessage("Telah Terjadi Kesalahan Pada Koneksi Anda.")
                    .setCancelable(false)
                    .setPositiveButton("Coba Lagi", new DialogInterface.OnClickListener() {
                @Override
                    public void onClick(DialogInterface dialog, int which) {
                        dialog.dismiss();
                        mulai();
                    }
                }).show();
            }
        }
    }, Utilities.rto());
}

private class Param {
    String x1d, type, key, token;
}

private class callAPI extends AsyncTask<Void, Void, Void> {

    private String code;
    private JSONObject json;
    private boolean background;


    @Override
    protected void onPreExecute() {
        super.onPreExecute();
        background = true;
        dialog = new ProgressDialog(v.getContext());
        dialog.setMessage("Sedang memproses data. Harap tunggu sejenak.");
        dialog.setCancelable(false);
        dialog.show();

    }

    @Override
    protected Void doInBackground(Void... voids) {
    try {
    StrictMode.ThreadPolicy policy = new StrictMode.ThreadPolicy.Builder().permitAll().build();
    StrictMode.setThreadPolicy(policy);


    Param param = new Param();
    param.x1d = sp.getString("username", "");
    param.type = "mmm";
    param.key = Utilities.imei(getActivity());
    param.token = sp.getString("token", "");
//                param.kd_kls = sp.getString("token", "");

    Gson gson = new Gson();
    List<NameValuePair> p = new ArrayList<NameValuePair>();
    p.add(new BasicNameValuePair("parsing", gson.toJson(param)));

    JsonParser jParser = new JsonParser();
    json = jParser.getJSONFromUrl(key.url(100), p);
    Log.e("param login ", gson.toJson(param));
    Log.e("isi json login", json.toString(2));
    code = json.getString("code");

} catch (Exception e) {
    background = false;
}
return null;
}

@Override
protected void onPostExecute(Void result) {
    super.onPostExecute(result);

    if (dialog.isShowing()) {
        dialog.dismiss();
    }
    handler.removeCallbacksAndMessages(null);

    if (background) {

        if (code.equals("OK4")) {
            proses();
        } else {
            AlertDialog.Builder ab = new AlertDialog.Builder(v.getContext());
            ab
                .setCancelable(false).setTitle("Informasi")
                .setMessage(code)
                .setPositiveButton("Tutup", new DialogInterface.OnClickListener() {
            @Override
                public void onClick(DialogInterface dialog, int which) {
                    dialog.dismiss();
                }
            })
        .show();
        }


    } else {
        Utilities.codeerror(v.getContext(), "ER0211");
    }
}

private void proses() {
    try {

    } catch (Exception e) {
        Log.e("ER___", String.valueOf(e));
    }
}
}