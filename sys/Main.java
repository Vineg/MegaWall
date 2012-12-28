import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;

import javax.swing.*;

public class Main{
	public static void main(String[] args){

		String srl=Main.class.getProtectionDomain().getCodeSource().getLocation().getPath();
		String packageName="";
		// if(pck!=null){
		// packageName=pck.getName();
		// if(className.startsWith(packageName))
		// className=className.substring(packageName.length()+1);
		// }
		// System.out.println(className);
		// URL url=cls.getResource(className);
		// String srl = ""+url;
//		srl=srl.substring(0, srl.lastIndexOf("/"));
		//if(srl.lastIndexOf("/")!=srl.length()-1){
			srl=srl.substring(0, srl.lastIndexOf("/"));
		//}
//		srl=srl.substring(srl.indexOf("/")+1, srl.length());
		System.out.println(srl);
		while(true){
			Thread th=Thread.currentThread();
			Process javacProcess = null;
			try {

				javacProcess = Runtime.getRuntime().exec("php "+srl+"/update.php");
				//javacProcess = Runtime.getRuntime().exec("php E:/AppServ/www/Blog/sys/update.php");
			} catch (IOException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			BufferedReader output = new BufferedReader(new InputStreamReader(javacProcess.getInputStream()));
			Reader r=new Reader(output);
			r.run();
			try {
				th.sleep(10000);
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}
			r.stop();

			System.out.println("wait for next");
			try {
				th.sleep(10000);
			} catch (InterruptedException e) {
				// TODO Auto-generated catch block
				e.printStackTrace();
			}

		}
	}
	static class Reader extends Thread{
		static boolean isend=false;
		static BufferedReader o;

		public Reader(BufferedReader output){o=output;}
		public void run(){
			String nextLine;
			try {
				while((nextLine = o.readLine()) != null)
				{
					System.out.println(nextLine);
				}
			} catch (IOException e) {e.printStackTrace();}
		}

	}
}